<?php

declare(strict_types=1);

use App\Notifications\AiDigestNotification;

it('returns database channel for via', function (): void {
    $notification = new AiDigestNotification('content', 'January 1, 2025');

    expect($notification->via(new stdClass))->toBe(['database']);
});

it('stores content and date', function (): void {
    $notification = new AiDigestNotification('test content', 'January 1, 2025');

    expect($notification->content)->toBe('test content')
        ->and($notification->date)->toBe('January 1, 2025');
});

it('accepts null content', function (): void {
    $notification = new AiDigestNotification(null, 'January 1, 2025');

    expect($notification->content)->toBeNull();
});
