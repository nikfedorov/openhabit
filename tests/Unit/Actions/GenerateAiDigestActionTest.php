<?php

declare(strict_types=1);

use App\Actions\GenerateAiDigestAction;
use App\Models\AiDigest;
use App\Models\AiModel;
use App\Models\DailyNote;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;
use App\Models\UserMemory;
use App\Services\RRuleService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Fake an OpenRouter JSON response containing the given digest + memory updates.
 *
 * @param  array<string, string>  $memoryUpdates
 */
function fakeAiJson(string $digest, array $memoryUpdates = []): void
{
    $content = json_encode(['digest' => $digest, 'memory_updates' => $memoryUpdates], JSON_THROW_ON_ERROR);

    Http::fake([
        'https://openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => $content]]],
        ]),
    ]);
}

function makeUserWithCompletionYesterday(): User
{
    $user = User::factory()->create(['timezone' => 'UTC']);
    $rule = resolve(RRuleService::class)->buildDaily();
    $habit = Habit::factory()->for($user)->create(['rrule' => $rule, 'is_active' => true]);
    HabitCompletion::factory()->for($habit)->for($user)->create(['completed_at' => now()->subDay()]);

    return $user;
}

beforeEach(function (): void {
    AiModel::factory()->create(['slug' => 'test-model', 'priority' => 1]);
});

it('creates digest with memory updates from habits data', function (): void {
    fakeAiJson('You did great!', [
        'long_term' => 'User has been consistent.',
        'successes' => 'Completed all habits today.',
    ]);
    $user = makeUserWithCompletionYesterday();

    $digest = resolve(GenerateAiDigestAction::class)->handle($user);

    expect($digest)->not->toBeNull()
        ->and($digest->content)->toBe('You did great!')
        ->and($digest->date->toDateString())->toBe(now()->subDay()->toDateString());

    $memories = $user->memories()->get()->keyBy(fn (UserMemory $m): string => $m->category->value);
    expect($memories)->toHaveCount(2)
        ->and($memories['long_term']->content)->toBe('User has been consistent.')
        ->and($memories['successes']->content)->toBe('Completed all habits today.');
});

it('returns null when API fails and does not create a digest', function (): void {
    Log::spy();
    Http::fake(['https://openrouter.ai/*' => Http::response(['error' => 'server error'], 500)]);
    $user = makeUserWithCompletionYesterday();

    expect(resolve(GenerateAiDigestAction::class)->handle($user))->toBeNull()
        ->and(AiDigest::query()->where('user_id', $user->id)->count())->toBe(0);
});

it('generates digest when only daily note is present', function (): void {
    fakeAiJson('Note-based digest.');
    $user = User::factory()->create(['timezone' => 'UTC']);
    DailyNote::factory()->for($user)->forDate(now()->subDay()->toDateString())->create([
        'content' => 'Felt tired today but stayed focused.',
    ]);

    $digest = resolve(GenerateAiDigestAction::class)->handle($user);

    expect($digest?->content)->toBe('Note-based digest.');
});

it('treats non-JSON response as plain-text digest with no memory updates', function (): void {
    Log::spy();
    Http::fake([
        'https://openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => 'Plain text response without JSON.']]],
        ]),
    ]);
    $user = makeUserWithCompletionYesterday();

    $digest = resolve(GenerateAiDigestAction::class)->handle($user);

    expect($digest?->content)->toBe('Plain text response without JSON.')
        ->and($user->memories()->count())->toBe(0);
});

it('updates existing digest on re-run for same date', function (): void {
    Http::fake([
        'https://openrouter.ai/*' => Http::sequence()
            ->push(['choices' => [['message' => ['content' => json_encode(['digest' => 'First digest.', 'memory_updates' => []], JSON_THROW_ON_ERROR)]]]])
            ->push(['choices' => [['message' => ['content' => json_encode(['digest' => 'Updated digest.', 'memory_updates' => []], JSON_THROW_ON_ERROR)]]]]),
    ]);
    $user = makeUserWithCompletionYesterday();
    $action = resolve(GenerateAiDigestAction::class);

    $action->handle($user);
    $action->handle($user);

    expect(AiDigest::query()->where('user_id', $user->id)->count())->toBe(1)
        ->and(AiDigest::query()->where('user_id', $user->id)->value('content'))->toBe('Updated digest.');
});
