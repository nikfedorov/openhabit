<?php

declare(strict_types=1);

use App\Jobs\SendAiDigestJob;
use App\Models\AiDigest;
use App\Models\DailyNote;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Setting;
use App\Models\User;
use App\Services\RRuleService;
use Illuminate\Support\Facades\Queue;

/**
 * Give the user a habit completion yesterday so they have trackable activity.
 */
function giveUserActivityYesterday(User $user): void
{
    $rule = resolve(RRuleService::class)->buildDaily();
    $habit = Habit::factory()->for($user)->create(['rrule' => $rule, 'is_active' => true]);
    HabitCompletion::factory()->for($habit)->for($user)->create(['completed_at' => now()->subDay()]);
}

/**
 * @param  array<string, mixed>  $attributes
 */
function makeEligibleUser(array $attributes = []): User
{
    return User::factory()->telegram()->premium()->create([
        'ai_digest_time' => '09:00',
        'timezone' => 'UTC',
        ...$attributes,
    ]);
}

beforeEach(function (): void {
    Queue::fake();
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '0']);
    $this->travelTo(today()->addHours(10));
});

it('dispatches job for eligible user', function (): void {
    $user = makeEligibleUser();
    giveUserActivityYesterday($user);

    $this->artisan('app:send-ai-digests')
        ->expectsOutputToContain('AI digest jobs dispatched: 1')
        ->assertExitCode(0);

    Queue::assertPushed(SendAiDigestJob::class, fn (SendAiDigestJob $job): bool => $job->userId === $user->id);
});

it('dispatches for user with only a daily note yesterday', function (): void {
    $user = makeEligibleUser();
    DailyNote::factory()->for($user)->forDate(now()->subDay()->toDateString())->create([
        'content' => 'Felt tired but pushed through.',
    ]);

    $this->artisan('app:send-ai-digests')
        ->expectsOutputToContain('AI digest jobs dispatched: 1')
        ->assertExitCode(0);

    Queue::assertPushed(SendAiDigestJob::class);
});

it('dispatches for a trialing user', function (): void {
    Setting::query()->where('key', 'trial_period_days')->update(['value' => '14']);

    $user = User::factory()->telegram()->create([
        'ai_digest_time' => '09:00',
        'timezone' => 'UTC',
        'subscription_expires_at' => null,
        'trial_banner_dismissed_at' => null,
        'created_at' => now()->subDays(5),
    ]);
    giveUserActivityYesterday($user);

    $this->artisan('app:send-ai-digests')
        ->expectsOutputToContain('AI digest jobs dispatched: 1')
        ->assertExitCode(0);

    Queue::assertPushed(SendAiDigestJob::class, fn (SendAiDigestJob $job): bool => $job->userId === $user->id);
});

/**
 * One test per ineligibility reason. Each sets up only the difference from the baseline.
 */
it('skips ineligible users', function (callable $setup): void {
    $setup();

    $this->artisan('app:send-ai-digests')
        ->expectsOutputToContain('AI digest jobs dispatched: 0')
        ->assertExitCode(0);

    Queue::assertNotPushed(SendAiDigestJob::class);
})->with([
    'no premium' => fn () => User::factory()->telegram()->create([
        'subscription_expires_at' => now()->subDay(),
        'ai_digest_time' => '09:00',
        'timezone' => 'UTC',
    ]),
    'already digested today' => function (): void {
        $user = makeEligibleUser();
        giveUserActivityYesterday($user);
        AiDigest::factory()->for($user)->create(['created_at' => now()]);
    },
    'before digest time' => function (): void {
        test()->travelTo(today()->addHours(8));
        makeEligibleUser();
    },
    'no ai_digest_time set' => fn (): User => makeEligibleUser(['ai_digest_time' => null]),
    'no activity yesterday' => makeEligibleUser(...),
]);
