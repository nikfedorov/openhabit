<?php

declare(strict_types=1);

use App\Actions\GenerateAiDigestAction;
use App\Jobs\SendAiDigestJob;
use App\Models\AiDigest;
use App\Models\User;
use App\Notifications\AiDigestNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

it('sends notification when digest is generated', function (): void {
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

it('does not send notification when no digest generated', function (): void {
    Notification::fake();

    $user = User::factory()->create();

    $action = new GenerateAiDigestAction;

    $job = new SendAiDigestJob($user->id);
    $job->handle($action);

    Notification::assertNothingSent();
});

it('logs warning when user not found', function (): void {
    $action = new GenerateAiDigestAction;

    $job = new SendAiDigestJob('00000000-0000-0000-0000-000000000000');

    Log::shouldReceive('warning')
        ->once()
        ->with('SendAiDigestJob: user not found', ['user_id' => '00000000-0000-0000-0000-000000000000']);

    $job->handle($action);
});

it('returns correct unique id', function (): void {
    $job = new SendAiDigestJob('test-user-id');

    expect($job->uniqueId())->toBe('test-user-id');
});

it('returns correct tags', function (): void {
    $job = new SendAiDigestJob('test-user-id');

    expect($job->tags())->toBe(['ai-digest', 'user:test-user-id']);
});

it('dispatches on ai-digests queue', function (): void {
    $job = new SendAiDigestJob('test-user-id');

    expect($job->queue)->toBe('ai-digests');
});
