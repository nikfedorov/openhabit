<?php

declare(strict_types=1);

namespace App\Http\Resources\Edit;

use App\Models\Habit;
use App\Services\RRuleService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full habit data for the edit form.
 *
 * @mixin Habit
 */
final class HabitFormResource extends JsonResource
{
    /**
     * @return array{id: int, name: string, description: string|null, iterations_required: int, is_active: bool, frequency: string, weekly_days: array<int>, monthly_days: array<int>, monthly_mode: string, monthly_position: int, monthly_weekday: int, notifications: AnonymousResourceCollection}
     */
    public function toArray(Request $request): array
    {
        $service = resolve(RRuleService::class);
        $rrule = $this->rrule ?? '';

        [
            'days' => $monthlyDays,
            'mode' => $monthlyMode,
            'position' => $monthlyPosition,
            'weekday' => $monthlyWeekday,
        ] = $this->parseMonthlyData($service, $rrule);

        return [
            /**
             * Unique habit identifier.
             *
             * @var int
             *
             * @example 1
             */
            'id' => $this->id,

            /**
             * Display name of the habit.
             *
             * @var string
             *
             * @example "Morning meditation"
             */
            'name' => $this->name,

            /**
             * Optional longer description.
             *
             * @var string|null
             *
             * @example "10 minutes before breakfast"
             */
            'description' => $this->description,

            /**
             * How many completions are required per day.
             *
             * @var int
             *
             * @example 1
             */
            'iterations_required' => $this->iterations_required,

            /**
             * Whether the habit is currently active and shown in the tracker.
             *
             * @var bool
             */
            'is_active' => $this->is_active,

            /**
             * RRule frequency: DAILY, WEEKLY, or MONTHLY.
             *
             * @var string
             *
             * @example "WEEKLY"
             */
            'frequency' => $this->frequency->value ?? 'DAILY',

            /**
             * Days of the week the habit repeats (0 = Monday … 6 = Sunday). For WEEKLY habits.
             *
             * @var array<int>
             *
             * @example [0, 2, 4]
             */
            'weekly_days' => $service->parseDaysOfWeek($rrule),

            /**
             * Specific calendar days of the month (1–31). For MONTHLY "day" mode.
             *
             * @var array<int>
             *
             * @example [1, 15]
             */
            'monthly_days' => $monthlyDays,

            /**
             * Monthly recurrence mode: "day" for specific dates, "position" for ordinal weekday.
             *
             * @var string
             *
             * @example "position"
             */
            'monthly_mode' => $monthlyMode,

            /**
             * Ordinal position within the month (1, 2, 3, 4, or -1 for last). Used when monthly_mode is "position".
             *
             * @var int
             *
             * @example 2
             */
            'monthly_position' => $monthlyPosition,

            /**
             * Day of the week for ordinal recurrence (0 = Monday … 6 = Sunday). Used when monthly_mode is "position".
             *
             * @var int
             *
             * @example 1
             */
            'monthly_weekday' => $monthlyWeekday,

            /**
             * Scheduled reminder notifications for this habit.
             */
            'notifications' => HabitNotificationResource::collection($this->notifications->sortBy('time')->values()),
        ];
    }

    /**
     * @return array{days: array<int>, mode: string, position: int, weekday: int}
     */
    private function parseMonthlyData(RRuleService $service, string $rrule): array
    {
        $ordinal = $service->parseOrdinalWeekday($rrule);

        if ($ordinal->ordinal !== null && $ordinal->day !== null) {
            return [
                'days' => [],
                'mode' => 'position',
                'position' => in_array($ordinal->ordinal, [1, 2, 3, 4, -1], true) ? $ordinal->ordinal : 1,
                'weekday' => $ordinal->day,
            ];
        }

        $daysOfMonth = $service->parseDaysOfMonth($rrule);

        return [
            'days' => array_values(array_filter($daysOfMonth, fn (int $d): bool => $d >= 1 && $d <= 31)),
            'mode' => 'day',
            'position' => 1,
            'weekday' => 0,
        ];
    }
}
