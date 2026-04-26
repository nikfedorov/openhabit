<?php

declare(strict_types=1);

use App\Actions\Settings\UpdateUserSettingsAction;
use App\Jobs\SetTelegramMenuButtonJob;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('dispatches SetTelegramMenuButtonJob when locale changes for a telegram user', function (): void {
    $user = User::factory()->create(['telegram_id' => '42', 'locale' => 'en']);

    Queue::fake();

    (new UpdateUserSettingsAction)->handle($user, ['locale' => 'ru']);

    Queue::assertPushed(SetTelegramMenuButtonJob::class, fn (SetTelegramMenuButtonJob $job): bool => $job->userId === $user->id);
});

it('does not dispatch SetTelegramMenuButtonJob when conditions are not met', function (array $attrs, array $data): void {
    $user = User::factory()->create($attrs);

    Queue::fake();

    (new UpdateUserSettingsAction)->handle($user, $data);

    Queue::assertNotPushed(SetTelegramMenuButtonJob::class);
})->with([
    'no telegram_id' => [['telegram_id' => null, 'locale' => 'en'], ['locale' => 'ru']],
    'locale not in update' => [['telegram_id' => '42', 'locale' => 'en'], ['theme' => 'dark']],
    'locale unchanged' => [['telegram_id' => '42', 'locale' => 'en'], ['locale' => 'en']],
]);

it('maps camelCase keys to snake_case columns and pads dayStartsAt with seconds', function (): void {
    $user = User::factory()->create(['day_starts_at' => '00:00:00', 'move_completed_to_end' => false]);

    (new UpdateUserSettingsAction)->handle($user, [
        'dayStartsAt' => '04:30',
        'moveCompletedToEnd' => true,
    ]);

    $user->refresh();

    expect((string) $user->day_starts_at)->toContain('04:30:00')
        ->and($user->move_completed_to_end)->toBeTrue();
});
