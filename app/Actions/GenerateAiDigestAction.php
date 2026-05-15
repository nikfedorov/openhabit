<?php

declare(strict_types=1);

namespace App\Actions;

use App\Ai\Agents\DailyDigestAgent;
use App\Ai\Support\DigestResult;
use App\Models\AiDigest;
use App\Models\AiLog;
use App\Models\AiModel;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\User;
use App\Services\AiPromptService;
use App\Services\RRuleService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Exceptions\FailoverableException;
use Laravel\Ai\Responses\AgentResponse;
use Throwable;

/**
 * Generates an AI digest for a user based on the previous day's habits and daily note.
 *
 * Iterates active {@see AiModel} rows in priority order and runs {@see DailyDigestAgent}
 * against each one until a model produces a digest. Failing models are temporarily disabled.
 */
final readonly class GenerateAiDigestAction
{
    /** Disable window for failover-style errors (rate limits, auth failures, etc.). */
    private const int FAILOVERABLE_DISABLE_HOURS = 24;

    /** Disable window for generic, unexpected errors. */
    private const int GENERIC_FAILURE_DISABLE_HOURS = 1;

    public function __construct(
        private AiPromptService $promptService,
        private RRuleService $rruleService,
    ) {}

    /**
     * Generate and store a digest for the given user.
     */
    public function handle(User $user): ?AiDigest
    {
        $yesterday = CarbonImmutable::now($user->timezone ?? 'UTC')->subDay()->startOfDay();

        $user->loadMissing(['aiTone', 'memories']);

        $habitsData = $this->gatherHabitsData($user, $yesterday);
        $dailyNote = $user->dailyNotes()->where('date', $yesterday->toDateString())->value('content');
        $recentDigests = $this->promptService->getRecentDigests($user);

        $userPrompt = $this->promptService->buildUserPrompt(
            $habitsData,
            is_string($dailyNote) ? $dailyNote : null,
            $recentDigests,
            $yesterday,
        );

        $digestText = $this->runAgent($user, $userPrompt);

        if ($digestText === null) {
            Log::warning('AI digest generation failed for user', ['user_id' => $user->id]);

            return null;
        }

        /** @var AiDigest $digest */
        $digest = $user->aiDigests()->updateOrCreate(
            ['date' => $yesterday->toDateString()],
            [
                'content' => $digestText,
                'habits_data' => $habitsData,
            ],
        );

        return $digest;
    }

    /**
     * Iterate active AiModel rows in priority order until one produces a digest.
     */
    private function runAgent(User $user, string $userPrompt): ?string
    {
        $models = AiModel::getOrdered();

        if ($models->isEmpty()) {
            Log::warning('No active AI models configured');

            return null;
        }

        foreach ($models as $aiModel) {
            $digest = $this->tryModel($user, $aiModel, $userPrompt);

            if ($digest !== null) {
                return $digest;
            }
        }

        return null;
    }

    /**
     * Attempt to generate a digest using a single model. Logs the call and
     * temporarily disables the model on failure.
     */
    private function tryModel(User $user, AiModel $aiModel, string $userPrompt): ?string
    {
        $result = new DigestResult;
        $agent = DailyDigestAgent::make(user: $user, result: $result);
        $systemPrompt = $agent->instructions();

        $startedAt = hrtime(true);
        $response = null;
        $error = null;

        try {
            $response = $agent->prompt(
                $userPrompt,
                provider: $aiModel->provider,
                model: $aiModel->slug,
            );

            if ($result->digest === null) {
                $error = 'Agent finished without calling TaskDone';
            }
        } catch (FailoverableException $exception) {
            $aiModel->disableFor(self::FAILOVERABLE_DISABLE_HOURS);
            $error = $exception->getMessage();
        } catch (Throwable $exception) {
            $aiModel->disableFor(self::GENERIC_FAILURE_DISABLE_HOURS);
            $error = $exception->getMessage();
        }

        $this->logCall(
            user: $user,
            aiModel: $aiModel,
            systemPrompt: $systemPrompt,
            userPrompt: $userPrompt,
            digest: $error === null ? $result->digest : null,
            response: $response,
            durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000),
            error: $error,
        );

        return $error === null ? $result->digest : null;
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

        $completionsByHabit = HabitCompletion::query()
            ->select(['habit_id', 'current_iteration'])
            ->whereIn('habit_id', $habits->modelKeys())
            ->whereDate('completed_at', $date->toDateString())
            ->get()
            ->keyBy('habit_id');

        return $habits->map(function (Habit $habit) use ($completionsByHabit, $date): array {
            $scheduled = $habit->rrule === null || $this->rruleService->matchesDate($habit->rrule, $date);
            $currentIteration = $completionsByHabit->get($habit->id)->current_iteration ?? 0;
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
     * Persist a single AI call log entry.
     */
    private function logCall(
        User $user,
        AiModel $aiModel,
        string $systemPrompt,
        string $userPrompt,
        ?string $digest,
        ?AgentResponse $response,
        int $durationMs,
        ?string $error,
    ): void {
        AiLog::query()->create([
            'user_id' => $user->id,
            'model' => $aiModel->slug,
            'system_prompt' => $systemPrompt,
            'user_prompt' => $userPrompt,
            'response' => $digest,
            'input_tokens' => $response?->usage->promptTokens,
            'output_tokens' => $response?->usage->completionTokens,
            'duration_ms' => $durationMs,
            'is_successful' => $error === null,
            'error' => $error,
        ]);
    }
}
