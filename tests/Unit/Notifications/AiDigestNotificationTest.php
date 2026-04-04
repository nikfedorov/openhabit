<?php

declare(strict_types=1);

use App\Notifications\AiDigestNotification;

test('via returns database channel', function (): void {
    $notification = new AiDigestNotification('content', 'January 1, 2025');

    expect($notification->via(new stdClass))->toBe(['database']);
});

test('stores content and date', function (): void {
    $notification = new AiDigestNotification('test content', 'January 1, 2025');

    expect($notification->content)->toBe('test content')
        ->and($notification->date)->toBe('January 1, 2025');
});

test('accepts null content', function (): void {
    $notification = new AiDigestNotification(null, 'January 1, 2025');

    expect($notification->content)->toBeNull();
});
