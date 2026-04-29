<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\AiDigest;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;
use App\Models\UserMemory;
use App\Services\AiPromptService;
use App\Services\AiService;
use App\Services\RRuleService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Generates an AI digest for a user based on the previous day's habits and daily note.
 */
final readonly class GenerateAiDigestAction
{
    public function __construct(
        private AiPromptService $promptService,
        private AiService $aiService,
        private RRuleService $rruleService,
    ) {}

    /**
     * Generate and store a digest for the given user.
     */
    public function handle(User $user): ?AiDigest
    {
        $yesterday = CarbonImmutable::now($user->timezone)->subDay()->startOfDay();

        $user->loadMissing(['aiTone', 'memories']);

        $habitsData = $this->gatherHabitsData($user, $yesterday);
        $dailyNote = $user->dailyNotes()->where('date', $yesterday->toDateString())->value('content');
        $recentDigests = $this->promptService->getRecentDigests($user);

        $systemPrompt = $this->promptService->buildSystemPrompt($user);
        $userPrompt = $this->promptService->buildUserPrompt(
            $habitsData,
            is_string($dailyNote) ? $dailyNote : null,
            $recentDigests,
            $yesterday,
        );

        $response = $this->aiService->generate($systemPrompt, $userPrompt, $user->id);

        if ($response === null) {
            Log::warning('AI digest generation failed for user', ['user_id' => $user->id]);

            return null;
        }

        $parsed = $this->promptService->parseResponse($response);

        /** @var AiDigest $digest */
        $digest = $user->aiDigests()->updateOrCreate(
            ['date' => $yesterday->toDateString()],
            [
                'content' => $parsed['digest'],
                'habits_data' => $habitsData,
            ],
        );

        $this->persistMemoryUpdates($user, $parsed['memory_updates']);

        return $digest;
    }

    /**
     * Gather habit data for a specific date.
     *
     * @return array<int, array{name: string, description: string|null, scheduled: bool, completed: bool, partial: bool, current_iteration: int, iterations_required: int}>
     */
    private function gatherHabitsData(User $user, CarbonImmutable $date): array
    {
        $habits = $user->habits()
            ->select(['id', 'name', 'description', 'rrule', 'iterations_required'])
            ->where('is_active', true)
            ->ordered()
            ->get();

        /** @var Collection<int, HabitCompletion> $completions */
        $completions = HabitCompletion::query()
            ->select(['habit_id', 'current_iteration'])
            ->whereIn('habit_id', $habits->pluck('id'))
            ->whereDate('completed_at', $date->toDateString())
            ->get()
            ->keyBy('habit_id');

        return $habits->map(function (Habit $habit) use ($completions, $date): array {
            $scheduled = $habit->rrule === null || $this->rruleService->matchesDate($habit->rrule, $date);
            $currentIteration = $completions->get($habit->id)->current_iteration ?? 0;
            $required = $habit->iterations_required;

            return [
                'name' => $habit->name ?? '',
                'description' => $habit->description,
                'scheduled' => $scheduled,
                'completed' => $scheduled && $currentIteration >= $required,
                'partial' => $scheduled && $currentIteration > 0 && $currentIteration < $required,
                'current_iteration' => $currentIteration,
                'iterations_required' => $required,
            ];
        })->all();
    }

    /**
     * Persist categorized memory updates in a single upsert.
     *
     * @param  array<string, string>  $memoryUpdates
     */
    private function persistMemoryUpdates(User $user, array $memoryUpdates): void
    {
        if ($memoryUpdates === []) {
            return;
        }

        $rows = array_map(
            fn (string $category, string $content): array => [
                'user_id' => $user->id,
                'category' => $category,
                'content' => $content,
            ],
            array_keys($memoryUpdates),
            array_values($memoryUpdates),
        );

        UserMemory::query()->upsert($rows, ['user_id', 'category'], ['content']);
    }
}
