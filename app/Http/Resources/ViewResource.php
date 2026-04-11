<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Visualization data for the View page (week, year, life tabs).
 *
 * @property-read array{tab: string, weekStart: string, weekEnd: string, weekStartFormatted: string, weekEndFormatted: string, weekEndFormattedFull: string, weekYear: string, isCurrentWeek: bool, franklinGrid: array<string, mixed>, selectedYear: int|null, birthdate: string|null, currentAge: int|null, lifeStats: array{currentAge: int, weeksLived: int, yearsRemaining: int}|null, weeklyActivityData: array<int, array{weekNum: int, intensity: int, completed: int, total: int}>|null, yearlyActivityData: array<int, array{year: int, intensity: int, completed: int, total: int}>|null, translations: array<string, string>} $resource
 */
final class ViewResource extends JsonResource
{
    /**
     * @return array{tab: string, weekStart: string, weekEnd: string, weekStartFormatted: string, weekEndFormatted: string, weekEndFormattedFull: string, weekYear: string, isCurrentWeek: bool, franklinGrid: array<string, mixed>, selectedYear: int|null, birthdate: string|null, currentAge: int|null, lifeStats: array{currentAge: int, weeksLived: int, yearsRemaining: int}|null, weeklyActivityData: array<int, array{weekNum: int, intensity: int, completed: int, total: int}>|null, yearlyActivityData: array<int, array{year: int, intensity: int, completed: int, total: int}>|null, translations: array<string, string>}
     */
    public function toArray(Request $request): array
    {
        return [
            /**
             * Active visualization tab.
             *
             * @var string
             *
             * @example "week"
             */
            'tab' => $this->resource['tab'],

            /**
             * Week start date (YYYY-MM-DD).
             *
             * @var string
             *
             * @example "2026-04-06"
             */
            'weekStart' => $this->resource['weekStart'],

            /**
             * Week end date (YYYY-MM-DD).
             *
             * @var string
             *
             * @example "2026-04-12"
             */
            'weekEnd' => $this->resource['weekEnd'],

            /**
             * Localized short start date.
             *
             * @var string
             *
             * @example "Apr 6"
             */
            'weekStartFormatted' => $this->resource['weekStartFormatted'],

            /**
             * Localized short end date.
             *
             * @var string
             *
             * @example "Apr 12"
             */
            'weekEndFormatted' => $this->resource['weekEndFormatted'],

            /**
             * Localized full end date with year.
             *
             * @var string
             *
             * @example "Apr 12, 2026"
             */
            'weekEndFormattedFull' => $this->resource['weekEndFormattedFull'],

            /**
             * Year of the displayed week.
             *
             * @var string
             *
             * @example "2026"
             */
            'weekYear' => $this->resource['weekYear'],

            /**
             * Whether the displayed week is the current week.
             *
             * @var bool
             */
            'isCurrentWeek' => $this->resource['isCurrentWeek'],

            /**
             * Weekly habit grid with day columns and habit rows.
             *
             * @var array{week_start: string, week_end: string, days: array<int, array{date: string, day_name: string, day_number: int, is_today: bool, is_future: bool}>, regular_habits: array<int, array{id: int, name: string, category: string|null, is_weekly_focus: bool, days: array<string, array{completed: bool, partial: bool, scheduled: bool}>}>, franklin_habits: array<int, array{id: int, name: string, category: string|null, is_weekly_focus: bool, days: array<string, array{completed: bool, partial: bool, scheduled: bool}>}>}
             */
            'franklinGrid' => $this->resource['franklinGrid'],

            /**
             * Selected year index (age) for the year tab.
             *
             * @var int|null
             *
             * @example 25
             */
            'selectedYear' => $this->resource['selectedYear'],

            /**
             * User's birthdate (YYYY-MM-DD).
             *
             * @var string|null
             *
             * @example "2001-03-15"
             */
            'birthdate' => $this->resource['birthdate'],

            /**
             * User's current age in years.
             *
             * @var int|null
             *
             * @example 25
             */
            'currentAge' => $this->resource['currentAge'],

            /**
             * Life statistics for the memento mori grid.
             *
             * @var array{currentAge: int, weeksLived: int, yearsRemaining: int}|null
             */
            'lifeStats' => $this->resource['lifeStats'],

            /**
             * Weekly activity data for the year heatmap.
             *
             * @var array<int, array{weekNum: int, intensity: int, completed: int, total: int}>|null
             */
            'weeklyActivityData' => $this->resource['weeklyActivityData'],

            /**
             * Yearly activity data for the life grid.
             *
             * @var array<int, array{year: int, intensity: int, completed: int, total: int}>|null
             */
            'yearlyActivityData' => $this->resource['yearlyActivityData'],

            /**
             * Localized UI strings for the view page.
             *
             * @var array<string, string>
             */
            'translations' => $this->resource['translations'],
        ];
    }
}
