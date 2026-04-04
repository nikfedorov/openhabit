<?php

declare(strict_types=1);

use App\Jobs\SendAiDigestJob;
use App\Models\AiDigest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

test('dispatches job for eligible user', function (): void {
    Queue::fake();
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '0']);

    $this->travelTo(today()->addHours(10));

    $user = User::factory()->telegram()->premium()->create([
        'ai_digest_time' => '09:00',
        'timezone' => 'UTC',
    ]);

    $this->artisan('app:send-ai-digests')
        ->expectsOutputToContain('AI digest jobs dispatched: 1')
        ->assertExitCode(0);

    Queue::assertPushed(SendAiDigestJob::class, fn (SendAiDigestJob $job): bool => $job->userId === $user->id);
});

test('skips user without premium', function (): void {
    Queue::fake();
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '0']);

    $this->travelTo(today()->addHours(10));

    User::factory()->telegram()->create([
        'subscription_expires_at' => now()->subDay(),
        'ai_digest_time' => '09:00',
        'timezone' => 'UTC',
    ]);

    $this->artisan('app:send-ai-digests')
        ->expectsOutputToContain('AI digest jobs dispatched: 0')
        ->assertExitCode(0);

    Queue::assertNothingPushed();
});

test('skips user already digested today', function (): void {
    Queue::fake();
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '0']);

    $this->travelTo(today()->addHours(10));

    $user = User::factory()->telegram()->premium()->create([
        'ai_digest_time' => '09:00',
        'timezone' => 'UTC',
    ]);
    AiDigest::factory()->for($user)->create(['created_at' => now()]);

    $this->artisan('app:send-ai-digests')
        ->expectsOutputToContain('AI digest jobs dispatched: 0')
        ->assertExitCode(0);

    Queue::assertNothingPushed();
});

test('skips user before digest time', function (): void {
    Queue::fake();
    Setting::factory()->create(['key' => 'trial_period_days', 'value' => '0']);

    $this->travelTo(today()->addHours(8));

    User::factory()->telegram()->premium()->create([
        'ai_digest_time' => '09:00',
        'timezone' => 'UTC',
    ]);

    $this->artisan('app:send-ai-digests')
        ->expectsOutputToContain('AI digest jobs dispatched: 0')
        ->assertExitCode(0);

    Queue::assertNothingPushed();
});

test('skips user without ai_digest_time', function (): void {
    Queue::fake();

    User::factory()->telegram()->premium()->create([
        'ai_digest_time' => null,
        'timezone' => 'UTC',
    ]);

    $this->artisan('app:send-ai-digests')
        ->expectsOutputToContain('AI digest jobs dispatched: 0')
        ->assertExitCode(0);

    Queue::assertNothingPushed();
});
