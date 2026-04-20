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
