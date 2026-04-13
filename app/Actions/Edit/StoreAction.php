<?php

declare(strict_types=1);

namespace App\Actions\Edit;

use App\Models\Habit;
use App\Models\User;
use App\Services\RRuleService;
use Illuminate\Support\Facades\DB;

/**
 * Create or update a habit from form data.
 */
final readonly class StoreAction
{
    public function __construct(private RRuleService $rruleService) {}

    /**
     * @param  array{name: string, description: ?string, frequency: string, iterations_required: int, is_active: bool, weekly_days: array<int>, monthly_days: array<int>, monthly_mode: string, monthly_position: int, monthly_weekday: int, notifications: array<int, array{time: string, is_active: bool}>}  $data
     */
    public function handle(User $user, array $data, ?Habit $habit = null): Habit
    {
        $rrule = $this->buildRRule($data);

        return DB::transaction(function () use ($user, $data, $habit, $rrule): Habit {
            $attributes = [
                'name' => $data['name'],
                'description' => $data['description'] ?: null,
                'iterations_required' => $data['iterations_required'],
                'is_active' => $data['is_active'],
                'rrule' => $rrule,
            ];

            if ($habit instanceof Habit) {
                $habit->update($attributes);
            } else {
                /** @var Habit $habit */
                $habit = $user->habits()->create($attributes);
            }

            $this->syncNotifications($habit, $data['notifications']);

            return $habit;
        });
    }

    /**
     * Build RRule string from form data.
     *
     * @param  array{frequency: string, weekly_days: array<int>, monthly_days: array<int>, monthly_mode: string, monthly_position: int, monthly_weekday: int}  $data
     */
    private function buildRRule(array $data): string
    {
        return match ($data['frequency']) {
            'DAILY' => $this->rruleService->buildDaily(),
            'WEEKLY' => $data['weekly_days'] !== []
                ? $this->rruleService->buildWeekly($data['weekly_days'])
                : $this->rruleService->buildDaily(),
            'MONTHLY' => $this->buildMonthlyRRule($data),
            default => $this->rruleService->buildDaily(),
        };
    }

    /**
     * @param  array{monthly_mode: string, monthly_position: int, monthly_weekday: int, monthly_days: array<int>}  $data
     */
    private function buildMonthlyRRule(array $data): string
    {
        if ($data['monthly_mode'] === 'position') {
            return $this->rruleService->buildMonthlyByWeekday($data['monthly_position'], $data['monthly_weekday']);
        }

        $sortedDays = $data['monthly_days'];
        sort($sortedDays);

        return $this->rruleService->buildMonthlyByDay($sortedDays);
    }

    /**
     * Sync notification times for a habit.
     *
     * @param  array<int, array{time: string, is_active: bool}>  $notifications
     */
    private function syncNotifications(Habit $habit, array $notifications): void
    {
        $habit->notifications()->delete();

        $unique = collect($notifications)->unique('time')->values()->all();

        $habit->notifications()->createMany($unique);
    }
}
