<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * @property-read array{date: string, dayName: string, dateFormatted: string, isToday: bool, habits: Collection<int, Habit>, totalHabits: int, completedCount: int, moveCompletedToEnd: bool, dailyNoteContent: string, activityData: array<int, array{date: string, percentage: float, completed: int, total: int, intensity: int}>, translations: array<string, string>} $resource
 */
final class TrackResource extends JsonResource
{
    /**
     * @return array{date: string, dayName: string, dateFormatted: string, isToday: bool, habits: AnonymousResourceCollection, totalHabits: int, completedCount: int, moveCompletedToEnd: bool, dailyNoteContent: string, activityData: array<int, array{date: string, percentage: float, completed: int, total: int, intensity: int}>, translations: array<string, string>}
     */
    public function toArray(Request $request): array
    {
        return [
            'date' => $this->resource['date'],
            'dayName' => $this->resource['dayName'],
            'dateFormatted' => $this->resource['dateFormatted'],
            'isToday' => $this->resource['isToday'],
            'habits' => HabitResource::collection($this->resource['habits']),
            'totalHabits' => $this->resource['totalHabits'],
            'completedCount' => $this->resource['completedCount'],
            'moveCompletedToEnd' => $this->resource['moveCompletedToEnd'],
            'dailyNoteContent' => $this->resource['dailyNoteContent'],
            'activityData' => $this->resource['activityData'],
            'translations' => $this->resource['translations'],
        ];
    }
}
