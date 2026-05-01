<?php

declare(strict_types=1);

namespace App\Actions\Track;

use App\Data\Track\DailyActivity;
use App\Data\Track\TrackData;
use App\Models\AiDigest;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Stat;
use App\Models\User;
use App\Services\RRuleService;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

final readonly class GetTrackDataAction
{
    public const int ACTIVITY_DAYS = 140;

    public function __construct(private RRuleService $rruleService) {}

    public static function activityCacheKey(User $user): string
    {
        return sprintf('user:%s:activity_data', $user->id);
    }

    public function handle(User $user, ?string $dateInput = null): TrackData
    {
        $today = $user->currentDate();
        $date = $dateInput !== null ? CarbonImmutable::parse($dateInput) : $today;
        if ($date->toDateString() > $today->toDateString()) {
            $date = $today;
        }

        $habitsData = $this->habitsData($user, $date);
        $totalHabits = $habitsData->count();
        $completedCount = $habitsData->filter(fn (Habit $habit): bool => (bool) $habit->getAttribute('is_completed'))->count();

        /** @var string|null $dailyNoteContent */
        $dailyNoteContent = $user->dailyNotes()
            ->where('date', $date->toDateString())
            ->value('content');

        return new TrackData(
            date: $date->toDateString(),
            dayName: Str::ucfirst($date->isoFormat('dddd')),
            dateFormatted: $date->isoFormat('LL'),
            isToday: $date->toDateString() === $today->toDateString(),
            habits: $habitsData,
            totalHabits: $totalHabits,
            completedCount: $completedCount,
            dailyNoteContent: $dailyNoteContent ?? '',
            activityData: $this->activityData($user, $today),
            translations: $this->translations(),
            aiDigest: $this->getAiDigest($user, $date),
        );
    }

    /**
     * @return Collection<int, Habit>
     */
    private function habitsData(User $user, CarbonImmutable $date): Collection
    {
        $habits = $user->habits()
            ->select(['id', 'user_id', 'name', 'description', 'iterations_required', 'rrule', 'sort_order'])
            ->where('is_active', true)
            ->ordered()
            ->get()
            ->filter(fn (Habit $habit): bool => $habit->rrule === null || $this->rruleService->matchesDate($habit->rrule, $date))
            ->values();

        $completions = $user->habitCompletions()
            ->select(['habit_id', 'current_iteration'])
            ->whereIn('habit_id', $habits->pluck('id'))
            ->whereDate('completed_at', $date)
            ->get()
            ->keyBy('habit_id');

        $habits->each(function (Habit $habit) use ($completions): void {
            /** @var HabitCompletion|null $completion */
            $completion = $completions->get($habit->id);
            $currentIteration = $completion !== null ? $completion->current_iteration : 0;

            $habit->setAttribute('current_iteration_for_date', $currentIteration);
            $habit->setAttribute('is_completed', $currentIteration >= $habit->iterations_required);
        });

        if ($user->move_completed_to_end) {
            return $habits->sortBy(fn (Habit $habit): bool => (bool) $habit->getAttribute('is_completed'))->values();
        }

        return $habits;
    }

    /**
     * @return array<int, DailyActivity>
     */
    private function activityData(User $user, CarbonImmutable $today): array
    {
        /** @var array<int, array{date: string, percentage: float, completed: int, total: int, intensity: int}> $cached */
        $cached = Cache::remember(
            key: self::activityCacheKey($user),
            ttl: 300,
            callback: fn (): array => $user->stats()
                ->select(['user_id', 'period_start', 'completed_count', 'planned_count'])
                ->daily()
                ->where('period_start', '>=', $today->subDays(self::ACTIVITY_DAYS - 1)->toDateString())
                ->orderBy('period_start')
                ->get()
                ->map(fn (Stat $stat): array => new DailyActivity(
                    date: $stat->period_start instanceof CarbonInterface
                        ? $stat->period_start->format('Y-m-d')
                        : (string) $stat->period_start,
                    percentage: $stat->completion_rate,
                    completed: $stat->completed_count,
                    total: $stat->planned_count,
                    intensity: $stat->intensity_level,
                )->toArray())
                ->all(),
        );

        return array_map(DailyActivity::fromArray(...), $cached);
    }

    /**
     * Get the AI digest for the given date.
     */
    private function getAiDigest(User $user, CarbonImmutable $date): ?AiDigest
    {
        return $user->aiDigests()
            ->select(['id', 'user_id', 'date', 'content'])
            ->where('date', $date->toDateString())
            ->first();
    }

    /**
     * @return array<string, string>
     */
    private function translations(): array
    {
        /** @var array<string, string> $translations */
        $translations = trans('track');
        $translations['last_n_days'] = __('track.last_n_days', ['count' => self::ACTIVITY_DAYS]);

        return $translations;
    }
}
