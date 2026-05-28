<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use App\Models\Habit;
use App\Models\HabitNotification;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection as BaseCollection;

#[Description('Disable premium features for users whose trial or subscription has expired')]
#[Signature('app:check-premium-expirations')]
final class CheckPremiumExpirationsCommand extends Command
{
    public function handle(): int
    {
        /** @var BaseCollection<int, string> $expiredUserIds */
        $expiredUserIds = User::query()
            ->withoutPremium()
            ->where(function (Builder $query): void {
                $query->whereNotNull('ai_digest_time')
                    ->orWhereHas('habits.notifications', fn (Builder $q): Builder => $q->where('is_active', true));
            })
            ->pluck('id');

        if ($expiredUserIds->isNotEmpty()) {
            User::query()
                ->whereIn('id', $expiredUserIds)
                ->whereNotNull('ai_digest_time')
                ->update(['ai_digest_time' => null]);

            $this->deactivateExtraNotifications($expiredUserIds);
        }

        $this->info('Users downgraded: '.$expiredUserIds->count());

        return self::SUCCESS;
    }

    /**
     * Keep only the earliest active notification per habit; deactivate the rest.
     *
     * @param  BaseCollection<int, string>  $userIds
     */
    private function deactivateExtraNotifications(BaseCollection $userIds): void
    {
        $habits = Habit::query()
            ->select('id')
            ->whereIn('user_id', $userIds)
            ->whereHas('notifications', fn (Builder $q): Builder => $q->where('is_active', true), '>=', 2)
            ->with(['notifications' => fn (Relation $q) => $q->where('is_active', true)->orderBy('time')->orderBy('id')])
            ->get();

        $idsToDeactivate = $habits->flatMap(fn (Habit $habit): BaseCollection => $habit->notifications->skip(1)->pluck('id'));

        if ($idsToDeactivate->isNotEmpty()) {
            HabitNotification::query()
                ->whereIn('id', $idsToDeactivate)
                ->update(['is_active' => false]);
        }
    }
}
