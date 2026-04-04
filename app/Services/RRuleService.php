<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RRuleFrequency;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Exception;
use RRule\RRule;

/**
 * Build, parse, and match iCalendar RRule strings (RFC 5545).
 *
 * All day-of-week parameters use ISO numbering: 0 = Monday … 6 = Sunday.
 *
 * @see https://www.rfc-editor.org/rfc/rfc5545
 */
final class RRuleService
{
    /** @var array<int, string> ISO day → RRule code */
    private const array DAY_CODES = [
        0 => 'MO', 1 => 'TU', 2 => 'WE', 3 => 'TH',
        4 => 'FR', 5 => 'SA', 6 => 'SU',
    ];

    /** @var array<string, int> RRule code → ISO day */
    private const array DAY_NUMBERS = [
        'MO' => 0, 'TU' => 1, 'WE' => 2, 'TH' => 3,
        'FR' => 4, 'SA' => 5, 'SU' => 6,
    ];

    /** @var array<int, string> ISO day → translation key */
    private const array DAY_KEYS = [
        0 => 'habit.monday', 1 => 'habit.tuesday', 2 => 'habit.wednesday', 3 => 'habit.thursday',
        4 => 'habit.friday', 5 => 'habit.saturday', 6 => 'habit.sunday',
    ];

    /** @var array<string, RRule<DateTimeInterface>> Per-request RRule object cache */
    private array $rruleCache = [];

    /** @var array<string, bool> Per-request matchesDate result cache */
    private array $matchesDateCache = [];

    // ========================================================================
    // Build
    // ========================================================================

    /**
     * @param  int  $interval  Every N days
     */
    public function buildDaily(int $interval = 1): string
    {
        return $this->buildRule('DAILY', $interval);
    }

    /**
     * @param  array<int>  $days  ISO days (0 = Monday … 6 = Sunday)
     * @param  int  $interval  Every N weeks
     */
    public function buildWeekly(array $days, int $interval = 1): string
    {
        return $this->buildRule('WEEKLY', $interval, $this->daysToByday($days));
    }

    /**
     * @param  array<int>  $days  Days of month (1–31)
     * @param  int  $interval  Every N months
     */
    public function buildMonthlyByDay(array $days = [1], int $interval = 1): string
    {
        $extra = $days !== [] ? 'BYMONTHDAY='.implode(',', $days) : null;

        return $this->buildRule('MONTHLY', $interval, $extra);
    }

    /**
     * @param  int  $ordinal  1–5 or -1 (last)
     * @param  int  $day  ISO day (0 = Monday … 6 = Sunday)
     * @param  int  $interval  Every N months
     */
    public function buildMonthlyByWeekday(int $ordinal, int $day, int $interval = 1): string
    {
        $dayCode = self::DAY_CODES[$day] ?? 'MO';

        return $this->buildRule('MONTHLY', $interval, sprintf('BYDAY=%d%s', $ordinal, $dayCode));
    }

    /**
     * @param  array<int>  $weeks  Weeks of year (1–52), all 7 weekdays included
     * @param  int  $interval  Every N years
     */
    public function buildYearly(array $weeks, int $interval = 1): string
    {
        $extra = [];

        if ($weeks !== []) {
            $extra[] = 'BYWEEKNO='.implode(',', $weeks);
        }

        $extra[] = 'BYDAY=MO,TU,WE,TH,FR,SA,SU';

        return $this->buildRule('YEARLY', $interval, implode(';', $extra));
    }

    // ========================================================================
    // Parse
    // ========================================================================

    public function getFrequency(string $rrule): ?RRuleFrequency
    {
        if (preg_match('/FREQ=(\w+)/', $rrule, $m)) {
            return RRuleFrequency::tryFrom($m[1]);
        }

        return null;
    }

    /**
     * Parse BYDAY simple day codes. Skips ordinal prefixes (1MO, -1FR).
     *
     * @return array<int> ISO day numbers
     */
    public function parseDaysOfWeek(string $rrule): array
    {
        if (! preg_match('/BYDAY=([^;]+)/', $rrule, $m)) {
            return [];
        }

        $days = [];

        foreach (explode(',', $m[1]) as $part) {
            if (preg_match('/^[A-Z]{2}$/', $part) && isset(self::DAY_NUMBERS[$part])) {
                $days[] = self::DAY_NUMBERS[$part];
            }
        }

        return $days;
    }

    /**
     * Parse ordinal weekday from BYDAY (e.g., "1MO" → [ordinal: 1, day: 0]).
     *
     * @return array{ordinal: int|null, day: int|null} day in ISO format
     */
    public function parseOrdinalWeekday(string $rrule): array
    {
        if (! preg_match('/BYDAY=(-?\d+)([A-Z]{2})/', $rrule, $m)) {
            return ['ordinal' => null, 'day' => null];
        }

        return [
            'ordinal' => (int) $m[1],
            'day' => self::DAY_NUMBERS[$m[2]] ?? null,
        ];
    }

    /**
     * @return array<int> Days (1–31)
     */
    public function parseDaysOfMonth(string $rrule): array
    {
        if (! preg_match('/BYMONTHDAY=([^;]+)/', $rrule, $m)) {
            return [];
        }

        return array_map(intval(...), explode(',', $m[1]));
    }

    /**
     * @return array<int> Weeks (1–52)
     */
    public function parseWeeksOfYear(string $rrule): array
    {
        if (! preg_match('/BYWEEKNO=([^;]+)/', $rrule, $m)) {
            return [];
        }

        return array_map(intval(...), explode(',', $m[1]));
    }

    // ========================================================================
    // Match & Occurrences
    // ========================================================================

    /**
     * Check if a date matches the recurrence pattern.
     */
    public function matchesDate(string $rrule, CarbonInterface $date): bool
    {
        $key = $rrule.'|'.$date->toDateString();

        return $this->matchesDateCache[$key] ??= $this->calculateMatchesDate($rrule, $date);
    }

    /**
     * @return array<DateTimeInterface>
     */
    public function getOccurrencesBetween(string $rrule, CarbonInterface $start, CarbonInterface $end): array
    {
        // Fast path: daily
        if ($rrule === 'FREQ=DAILY') {
            return $this->generateDailyOccurrences($start, $end);
        }

        // Fast path: simple weekly (no INTERVAL)
        if (str_starts_with($rrule, 'FREQ=WEEKLY') && ! str_contains($rrule, 'INTERVAL')) {
            $days = $this->parseDaysOfWeek($rrule);

            if ($days !== []) {
                return $this->generateWeeklyOccurrences($start, $end, $days);
            }
        }

        try {
            /** @var array<DateTimeInterface> */
            return $this->getCachedRRule($rrule, $start)->getOccurrencesBetween($start, $end);
        } catch (Exception) {
            return [];
        }
    }

    public function isValid(string $rrule): bool
    {
        try {
            new RRule($rrule);

            return true;
        } catch (Exception) {
            return false;
        }
    }

    // ========================================================================
    // Description
    // ========================================================================

    /**
     * Human-readable description for display.
     */
    public function getDescription(string $rrule): string
    {
        $frequency = $this->getFrequency($rrule);

        if (! $frequency instanceof RRuleFrequency) {
            return $rrule;
        }

        return match ($frequency) {
            RRuleFrequency::Daily => $this->describeDailyRule($rrule),
            RRuleFrequency::Weekly => $this->describeWeeklyRule($rrule),
            RRuleFrequency::Monthly => $this->describeMonthlyRule($rrule),
            RRuleFrequency::Yearly => $this->describeYearlyRule($rrule),
        };
    }

    // ========================================================================
    // Private helpers
    // ========================================================================

    private function buildRule(string $freq, int $interval, ?string $extra = null): string
    {
        $parts = ['FREQ='.$freq];

        if ($interval > 1) {
            $parts[] = 'INTERVAL='.$interval;
        }

        if ($extra !== null) {
            $parts[] = $extra;
        }

        return implode(';', $parts);
    }

    /**
     * @param  array<int>  $days  ISO days
     */
    private function daysToByday(array $days): ?string
    {
        if ($days === []) {
            return null;
        }

        $codes = array_map(fn (int $d): string => self::DAY_CODES[$d] ?? 'MO', $days);

        return 'BYDAY='.implode(',', $codes);
    }

    private function calculateMatchesDate(string $rrule, CarbonInterface $date): bool
    {
        if ($rrule === 'FREQ=DAILY') {
            return true;
        }

        // Fast path: weekly without INTERVAL — direct day-of-week check
        if (str_starts_with($rrule, 'FREQ=WEEKLY') && ! str_contains($rrule, 'INTERVAL')) {
            $days = $this->parseDaysOfWeek($rrule);

            if ($days !== []) {
                // dayOfWeekIso: 1=Mon…7=Sun → subtract 1 to match ISO 0=Mon…6=Sun
                return in_array($date->dayOfWeekIso - 1, $days, true);
            }
        }

        try {
            $occurrences = $this->getCachedRRule($rrule)->getOccurrencesBetween(
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
            );

            return $occurrences !== [];
        } catch (Exception) {
            return false;
        }
    }

    /**
     * @return array<DateTimeInterface>
     */
    private function generateDailyOccurrences(CarbonInterface $start, CarbonInterface $end): array
    {
        $occurrences = [];
        $current = $start->copy()->startOfDay();
        $endDay = $end->copy()->endOfDay();
        $limit = 400;

        while ($current->lte($endDay) && $limit-- > 0) {
            $occurrences[] = $current->toDateTime();
            $current = $current->addDay();
        }

        return $occurrences;
    }

    /**
     * @param  array<int>  $days  ISO day numbers
     * @return array<DateTimeInterface>
     */
    private function generateWeeklyOccurrences(CarbonInterface $start, CarbonInterface $end, array $days): array
    {
        $occurrences = [];
        $current = $start->copy()->startOfDay();
        $endDay = $end->copy()->endOfDay();
        $limit = 400;

        while ($current->lte($endDay) && $limit-- > 0) {
            if (in_array($current->dayOfWeekIso - 1, $days, true)) {
                $occurrences[] = $current->toDateTime();
            }

            $current = $current->addDay();
        }

        return $occurrences;
    }

    private function describeDailyRule(string $rrule): string
    {
        if (preg_match('/INTERVAL=(\d+)/', $rrule, $m)) {
            $interval = (int) $m[1];

            return $interval === 1 ? __('habit.every_day') : __('habit.every_n_days', ['count' => $interval]);
        }

        return __('habit.every_day');
    }

    private function describeWeeklyRule(string $rrule): string
    {
        $days = $this->parseDaysOfWeek($rrule);

        if ($days === [] || count($days) === 7) {
            return __('habit.every_day');
        }

        if ($days === [0, 1, 2, 3, 4]) {
            return __('habit.weekdays');
        }

        if (count($days) === 2 && in_array(5, $days, true) && in_array(6, $days, true)) {
            return __('habit.weekends');
        }

        $names = array_map(fn (int $d): string => __(self::DAY_KEYS[$d] ?? (string) $d), $days);

        return implode(', ', $names);
    }

    private function describeMonthlyRule(string $rrule): string
    {
        $daysOfMonth = $this->parseDaysOfMonth($rrule);

        if ($daysOfMonth !== []) {
            return __('habit.day_of_month', ['days' => implode(', ', $daysOfMonth)]);
        }

        $ordinal = $this->parseOrdinalWeekday($rrule);

        if ($ordinal['ordinal'] !== null && $ordinal['day'] !== null) {
            $ordinalKeys = [
                1 => 'habit.position_1st', 2 => 'habit.position_2nd',
                3 => 'habit.position_3rd', 4 => 'habit.position_4th',
                -1 => 'habit.position_last',
            ];
            $ordinalName = isset($ordinalKeys[$ordinal['ordinal']])
                ? __($ordinalKeys[$ordinal['ordinal']])
                : '#'.$ordinal['ordinal'];
            $dayName = __(self::DAY_KEYS[$ordinal['day']] ?? 'day');

            return __('habit.ordinal_weekday_of_month', ['ordinal' => $ordinalName, 'day' => $dayName]);
        }

        return __('habit.monthly');
    }

    private function describeYearlyRule(string $rrule): string
    {
        $weeks = $this->parseWeeksOfYear($rrule);

        if ($weeks !== []) {
            return __('habit.week_of_year', ['weeks' => implode(', ', $weeks)]);
        }

        return __('habit.yearly');
    }

    /**
     * @return RRule<DateTimeInterface>
     */
    private function getCachedRRule(string $rrule, ?CarbonInterface $start = null): RRule
    {
        $key = $rrule.'|'.($start?->format('Y-m-d') ?? '2000-01-01');

        return $this->rruleCache[$key] ??= $this->createRRule($rrule, $start);
    }

    /**
     * @return RRule<DateTimeInterface>
     */
    private function createRRule(string $rrule, ?CarbonInterface $start = null): RRule
    {
        /** @var array<string, string> $parts */
        $parts = [];

        foreach (explode(';', $rrule) as $segment) {
            $kv = explode('=', $segment, 2);

            if (count($kv) === 2) {
                $parts[$kv[0]] = $kv[1];
            }
        }

        if (! isset($parts['DTSTART'])) {
            $parts['DTSTART'] = $start?->format('Y-m-d') ?? '2000-01-01';
        }

        return new RRule($parts);
    }
}
