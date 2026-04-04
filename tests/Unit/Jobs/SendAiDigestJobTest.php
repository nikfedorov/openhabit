<?php

declare(strict_types=1);

use App\Actions\GenerateAiDigestAction;
use App\Jobs\SendAiDigestJob;
use App\Models\AiDigest;
use App\Models\User;
use App\Notifications\AiDigestNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

test('sends notification when digest is generated', function (): void {
    Notification::fake();

    $user = User::factory()->create(['locale' => 'en']);
    AiDigest::factory()->for($user)->create([
        'date' => now(),
        'content' => 'Test digest content',
    ]);

    $action = new GenerateAiDigestAction;

    $job = new SendAiDigestJob($user->id);
    $job->handle($action);

    Notification::assertSentTo($user, AiDigestNotification::class);
});

test('does not send notification when no digest generated', function (): void {
    Notification::fake();

    $user = User::factory()->create();

    $action = new GenerateAiDigestAction;

    $job = new SendAiDigestJob($user->id);
    $job->handle($action);

    Notification::assertNothingSent();
});

test('logs warning when user not found', function (): void {
    $action = new GenerateAiDigestAction;

    $job = new SendAiDigestJob('00000000-0000-0000-0000-000000000000');

    Log::shouldReceive('warning')
        ->once()
        ->with('SendAiDigestJob: user not found', ['user_id' => '00000000-0000-0000-0000-000000000000']);

    $job->handle($action);
});

test('returns correct unique id', function (): void {
    $job = new SendAiDigestJob('test-user-id');

    expect($job->uniqueId())->toBe('test-user-id');
});

test('returns correct tags', function (): void {
    $job = new SendAiDigestJob('test-user-id');

    expect($job->tags())->toBe(['ai-digest', 'user:test-user-id']);
});

test('is dispatched on ai-digests queue', function (): void {
    $job = new SendAiDigestJob('test-user-id');

    expect($job->queue)->toBe('ai-digests');
});
