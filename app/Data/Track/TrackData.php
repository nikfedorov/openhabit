<?php

declare(strict_types=1);

namespace App\Data\Track;

use App\Models\AiDigest;
use App\Models\Habit;
use Illuminate\Support\Collection;

/**
 * Data returned by Track GetTrackDataAction.
 */
final readonly class TrackData
{
    /**
     * @param  Collection<int, Habit>  $habits
     * @param  array<int, DailyActivity>  $activityData
     * @param  array<string, string>  $translations
     */
    public function __construct(
        public string $date,
        public string $dayName,
        public string $dateFormatted,
        public bool $isToday,
        public Collection $habits,
        public int $totalHabits,
        public int $completedCount,
        public string $dailyNoteContent,
        public array $activityData,
        public array $translations,
        public ?AiDigest $aiDigest = null,
    ) {}
}
