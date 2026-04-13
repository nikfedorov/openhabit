<?php

declare(strict_types=1);

namespace App\Actions\Edit;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Reorder user's regular habits by updating sort_order.
 */
final readonly class ReorderAction
{
    public const int MIN_SORT_ORDER = 101;

    /**
     * @param  array<int>  $orderedIds  Habit IDs in desired order.
     */
    public function handle(User $user, array $orderedIds): void
    {
        $validIds = $user->habits()
            ->excludingFranklinVirtues()
            ->whereIn('id', $orderedIds)
            ->pluck('id')
            ->all();

        $updates = [];

        foreach ($orderedIds as $position => $habitId) {
            if (in_array($habitId, $validIds, true)) {
                $updates[$habitId] = $position + self::MIN_SORT_ORDER;
            }
        }

        if ($updates === []) {
            return;
        }

        DB::transaction(function () use ($updates): void {
            foreach ($updates as $id => $order) {
                Habit::query()->where('id', $id)->update(['sort_order' => $order]);
            }
        });
    }
}
