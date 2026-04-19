<?php

declare(strict_types=1);

use App\Actions\GenerateAiDigestAction;
use App\Jobs\SendAiDigestJob;
use App\Models\AiModel;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;
use App\Notifications\AiDigestNotification;
use App\Services\RRuleService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

beforeEach(function (): void {
    config()->set('services.openrouter.api_key', 'test-key');
    config()->set('services.openrouter.base_url', 'https://openrouter.ai/api/v1');
    AiModel::factory()->create(['slug' => 'test-model', 'priority' => 1]);
});

it('sends notification when digest is generated', function (): void {
    Notification::fake();

    Http::fake([
        'https://openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => json_encode([
                'digest' => 'Test digest content',
                'memory_updates' => [],
            ], JSON_THROW_ON_ERROR)]]],
        ]),
    ]);

    $user = User::factory()->create(['locale' => 'en', 'timezone' => 'UTC']);
    $rule = resolve(RRuleService::class)->buildDaily();
    $habit = Habit::factory()->for($user)->create(['rrule' => $rule, 'is_active' => true]);
    HabitCompletion::factory()->for($habit)->for($user)->create([
        'completed_at' => now()->subDay(),
    ]);

    new SendAiDigestJob($user->id)->handle(resolve(GenerateAiDigestAction::class));

    Notification::assertSentTo($user, AiDigestNotification::class);
});

it('does not send notification when no digest generated', function (): void {
    Notification::fake();

    Http::fake([
        'https://openrouter.ai/*' => Http::response(['error' => 'server error'], 500),
    ]);

    $user = User::factory()->create();

    new SendAiDigestJob($user->id)->handle(resolve(GenerateAiDigestAction::class));

    Notification::assertNothingSent();
});

it('logs warning when user not found', function (): void {
    Http::fake();

    $job = new SendAiDigestJob('00000000-0000-0000-0000-000000000000');

    Log::shouldReceive('warning')
        ->once()
        ->with('SendAiDigestJob: user not found', ['user_id' => '00000000-0000-0000-0000-000000000000']);

    $job->handle(resolve(GenerateAiDigestAction::class));
});

it('exposes unique id, tags, and queue', function (): void {
    $job = new SendAiDigestJob('test-user-id');

    expect($job->uniqueId())->toBe('test-user-id')
        ->and($job->tags())->toBe(['ai-digest', 'user:test-user-id'])
        ->and($job->queue)->toBe('ai-digests');
});
