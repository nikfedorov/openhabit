<?php

declare(strict_types=1);

namespace App\Telegram\Callbacks;

use App\Models\Habit;
use App\Models\HabitCompletion;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\WebApp\WebAppInfo;

use function app;

/**
 * Handles the "complete_habit:{id}" callback query from Telegram inline buttons.
 *
 * Creates a completion record for today if one doesn't already exist,
 * then updates the inline keyboard to reflect the completed state.
 */
final class CompleteHabitCallback
{
    public function __invoke(Nutgram $bot, string $habitId): void
    {
        $habit = Habit::query()
            ->with('user:id,timezone,locale,day_starts_at')
            ->whereRelation('user', 'telegram_id', (string) $bot->userId())
            ->find((int) $habitId);

        if ($habit === null || $habit->user === null) {
            $bot->answerCallbackQuery(text: __('telegram.habit_not_found'));

            return;
        }

        $user = $habit->user;
        app()->setLocale($user->preferredLocale());
        $today = $user->currentDate()->toDateString();

        // Check if already completed today
        $alreadyCompleted = $habit->completions()
            ->whereDate('completed_at', $today)
            ->exists();

        if ($alreadyCompleted) {
            $bot->answerCallbackQuery(text: __('telegram.already_completed'));

            return;
        }

        // Create completion record
        HabitCompletion::query()->create([
            'habit_id' => $habit->id,
            'user_id' => $user->id,
            'completed_at' => $today,
            'current_iteration' => 1,
        ]);

        // Update the inline keyboard to show completed state
        $bot->editMessageReplyMarkup(
            reply_markup: $this->buildCompletedKeyboard(),
        );

        $bot->answerCallbackQuery(text: __('telegram.marked_as_done', ['name' => $habit->name]));
    }

    /**
     * Build the inline keyboard reflecting the completed state.
     */
    private function buildCompletedKeyboard(): InlineKeyboardMarkup
    {
        return InlineKeyboardMarkup::make()
            ->addRow(
                InlineKeyboardButton::make(
                    text: __('telegram.done'),
                    callback_data: 'noop',
                ),
                InlineKeyboardButton::make(
                    text: __('telegram.open_app'),
                    web_app: WebAppInfo::make(url('/telegram-miniapp')),
                ),
            );
    }
}
