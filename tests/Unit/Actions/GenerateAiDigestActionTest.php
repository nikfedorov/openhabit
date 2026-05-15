<?php

declare(strict_types=1);

use App\Actions\GenerateAiDigestAction;
use App\Ai\Agents\DailyDigestAgent;
use App\Models\AiDigest;
use App\Models\AiLog;
use App\Models\AiModel;
use App\Models\AiTone;
use App\Models\DailyNote;
use App\Models\Habit;
use App\Models\Setting;
use App\Models\User;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Exceptions\RateLimitedException;
use Laravel\Ai\Responses\Data\ToolCall;

beforeEach(function (): void {
    Setting::query()->upsert(
        [['key' => 'ai_system_prompt', 'value' => 'System prompt.', 'type' => 'text']],
        ['key'],
        ['value'],
    );
});

function makeDigestUser(): User
{
    $tone = AiTone::factory()->create(['system_instruction' => 'Be kind.']);
    $user = User::factory()->create(['timezone' => 'UTC', 'locale' => 'en', 'ai_tone_id' => $tone->id]);
    Habit::factory()->for($user)->create(['is_active' => true, 'rrule' => null, 'iterations_required' => 1]);
    DailyNote::factory()->for($user)->create(['date' => now('UTC')->subDay()->toDateString(), 'content' => 'Good day.']);

    return $user;
}

it('stores the digest delivered via the TaskDone tool and logs the call', function (): void {
    $user = makeDigestUser();
    AiModel::factory()->create(['slug' => 'gpt-4o', 'provider' => Lab::OpenAI, 'priority' => 1]);

    DailyDigestAgent::fake([
        new ToolCall('1', 'TaskDone', ['digest' => 'A solid day.']),
        'wrap-up',
    ]);

    $digest = resolve(GenerateAiDigestAction::class)->handle($user);

    expect($digest)->toBeInstanceOf(AiDigest::class)
        ->and($digest->content)->toBe('A solid day.');

    expect(AiLog::query()->where('user_id', $user->id)->sole())
        ->is_successful->toBeTrue()
        ->model->toBe('gpt-4o');
});

it('rotates to the next model on a failover exception and disables the failed one', function (): void {
    $user = makeDigestUser();
    $modelA = AiModel::factory()->create(['slug' => 'model-a', 'provider' => Lab::OpenAI, 'priority' => 1]);
    AiModel::factory()->create(['slug' => 'model-b', 'provider' => Lab::OpenRouter, 'priority' => 2]);

    $attempt = 0;
    DailyDigestAgent::fake(function () use (&$attempt): ToolCall|string {
        $attempt++;
        if ($attempt === 1) {
            throw RateLimitedException::forProvider('openai');
        }

        return $attempt === 2
            ? new ToolCall('1', 'TaskDone', ['digest' => 'From model B.'])
            : 'wrap-up';
    });

    $digest = resolve(GenerateAiDigestAction::class)->handle($user);

    expect($digest?->content)->toBe('From model B.')
        ->and($modelA->refresh()->disabled_until?->isFuture())->toBeTrue()
        ->and(AiLog::query()->count())->toBe(2);
});

it('returns null when no active models are configured', function (): void {
    $tone = AiTone::factory()->create();
    $user = User::factory()->create(['ai_tone_id' => $tone->id]);

    expect(resolve(GenerateAiDigestAction::class)->handle($user))->toBeNull();
});

it('disables the model for one hour on a generic throwable', function (): void {
    $user = makeDigestUser();
    $model = AiModel::factory()->create(['slug' => 'model-x', 'provider' => Lab::OpenAI, 'priority' => 1]);

    DailyDigestAgent::fake(function (): never {
        throw new RuntimeException('boom');
    });

    $digest = resolve(GenerateAiDigestAction::class)->handle($user);

    expect($digest)->toBeNull()
        ->and($model->refresh()->disabled_until?->isFuture())->toBeTrue();
});

it('logs an error when the agent finishes without calling TaskDone', function (): void {
    $user = makeDigestUser();
    AiModel::factory()->create(['slug' => 'lazy-model', 'provider' => Lab::OpenAI, 'priority' => 1]);

    DailyDigestAgent::fake(['just text, no tool call']);

    expect(resolve(GenerateAiDigestAction::class)->handle($user))->toBeNull();

    expect(AiLog::query()->where('user_id', $user->id)->sole())
        ->is_successful->toBeFalse()
        ->error->toContain('TaskDone');
});
