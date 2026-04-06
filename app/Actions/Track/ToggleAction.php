<?php

declare(strict_types=1);

namespace App\Actions\Track;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

final readonly class ToggleAction
{
    public function handle(User $user, int $habitId, string $date): void
    {
        $habit = $user->habits()
            ->select(['id', 'user_id', 'iterations_required'])
            ->where('id', $habitId)
            ->firstOrFail();

        $completion = $user->habitCompletions()
            ->where('habit_id', $habit->id)
            ->whereDate('completed_at', $date)
            ->first();

        if ($completion !== null) {
            if ($completion->current_iteration < $habit->iterations_required) {
                $completion->update([
                    'current_iteration' => $completion->current_iteration + 1,
                ]);
            } else {
                $completion->setRelation('user', $user);
                $completion->delete();
            }
        } else {
            $user->habitCompletions()->create([
                'habit_id' => $habit->id,
                'completed_at' => $date,
                'current_iteration' => 1,
            ]);
        }

        Cache::forget(IndexAction::activityCacheKey($user));
    }
}
