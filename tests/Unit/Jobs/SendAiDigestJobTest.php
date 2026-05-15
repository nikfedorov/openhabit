<?php

declare(strict_types=1);

use App\Actions\GenerateAiDigestAction;
use App\Ai\Agents\DailyDigestAgent;
use App\Jobs\SendAiDigestJob;
use App\Models\AiModel;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\AiDigestNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Responses\Data\ToolCall;

it('sends notification when the action returns a digest', function (): void {
    Notification::fake();
    Setting::query()->upsert(
        [['key' => 'ai_system_prompt', 'value' => 'p', 'type' => 'text']],
        ['key'],
        ['value'],
    );
    AiModel::factory()->create(['slug' => 'test-model', 'provider' => Lab::OpenAI, 'priority' => 1]);

    DailyDigestAgent::fake([new ToolCall('1', 'TaskDone', ['digest' => 'hello'])]);

    $user = User::factory()->create(['locale' => 'en', 'timezone' => 'UTC']);

    new SendAiDigestJob($user->id)->handle(resolve(GenerateAiDigestAction::class));

    Notification::assertSentTo($user, AiDigestNotification::class);
});

it('does not send notification when no digest is generated', function (): void {
    Notification::fake();

    // No AiModel rows configured => action returns null without invoking any agent.
    $user = User::factory()->create();

    new SendAiDigestJob($user->id)->handle(resolve(GenerateAiDigestAction::class));

    Notification::assertNothingSent();
});

it('logs a warning when the user does not exist and skips the action', function (): void {
    Log::shouldReceive('warning')
        ->once()
        ->with('SendAiDigestJob: user not found', ['user_id' => 99999999]);

    new SendAiDigestJob(99999999)->handle(resolve(GenerateAiDigestAction::class));
});

it('exposes unique id, tags, and queue', function (): void {
    $job = new SendAiDigestJob(42);

    expect($job->uniqueId())->toBe('42')
        ->and($job->tags())->toBe(['ai-digest', 'user:42'])
        ->and($job->queue)->toBe('ai-digests');
});
