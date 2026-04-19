<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AiLog;
use App\Models\AiModel;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client for OpenAI-compatible chat completions APIs.
 *
 * Each AiModel stores its own base_url and api_key, so any
 * provider with an OpenAI-compatible endpoint can be used.
 *
 * Supports model rotation: tries active models in priority order,
 * falling back to the next model on failure (429, 5xx, connection errors).
 */
final class AiService
{
    private const int TIMEOUT_SECONDS = 10;

    private const float TEMPERATURE = 0.7;

    private const int RATE_LIMIT_DISABLE_HOURS = 24;

    private const int SERVER_ERROR_DISABLE_HOURS = 1;

    /**
     * Generate a chat completion with automatic model rotation.
     *
     * @return string|null The generated text, or null if all models fail.
     */
    public function generate(string $systemPrompt, string $userPrompt, ?string $userId = null): ?string
    {
        $models = AiModel::getOrdered();

        if ($models->isEmpty()) {
            Log::warning('No active AI models configured');

            return null;
        }

        foreach ($models as $aiModel) {
            $content = $this->tryModel($aiModel, $systemPrompt, $userPrompt, $userId);

            if ($content !== null) {
                return $content;
            }
        }

        return null;
    }

    /**
     * Attempt a single model call, logging the outcome.
     *
     * @return string|null Content on success, null on failure (try next model).
     */
    private function tryModel(
        AiModel $aiModel,
        string $systemPrompt,
        string $userPrompt,
        ?string $userId,
    ): ?string {
        $startTime = hrtime(true);

        try {
            $request = Http::timeout(self::TIMEOUT_SECONDS);

            if ($aiModel->api_key !== null && $aiModel->api_key !== '') {
                $request = $request->withToken($aiModel->api_key);
            }

            $response = $request->post($aiModel->base_url.'/chat/completions', [
                'model' => $aiModel->slug,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => self::TEMPERATURE,
            ]);
        } catch (ConnectionException $connectionException) {
            $aiModel->disableFor(self::SERVER_ERROR_DISABLE_HOURS);
            $this->logCall($userId, $aiModel, $systemPrompt, $userPrompt, $this->elapsedMs($startTime), error: $connectionException->getMessage());

            return null;
        }

        $durationMs = $this->elapsedMs($startTime);

        if ($response->failed()) {
            return $this->handleFailedResponse($aiModel, $response, $systemPrompt, $userPrompt, $userId, $durationMs);
        }

        return $this->handleSuccessResponse($aiModel, $response, $systemPrompt, $userPrompt, $userId, $durationMs);
    }

    private function handleFailedResponse(
        AiModel $aiModel,
        Response $response,
        string $systemPrompt,
        string $userPrompt,
        ?string $userId,
        int $durationMs,
    ): null {
        if ($response->status() === 429) {
            $aiModel->disableFor(self::RATE_LIMIT_DISABLE_HOURS);
        } elseif ($response->serverError()) {
            $aiModel->disableFor(self::SERVER_ERROR_DISABLE_HOURS);
        }

        $this->logCall($userId, $aiModel, $systemPrompt, $userPrompt, $durationMs,
            error: sprintf('HTTP %d: %s', $response->status(), $response->body()),
        );

        return null;
    }

    private function handleSuccessResponse(
        AiModel $aiModel,
        Response $response,
        string $systemPrompt,
        string $userPrompt,
        ?string $userId,
        int $durationMs,
    ): ?string {
        /** @var string|null $content */
        $content = $response->json('choices.0.message.content');
        /** @var int|null $inputTokens */
        $inputTokens = $response->json('usage.prompt_tokens');
        /** @var int|null $outputTokens */
        $outputTokens = $response->json('usage.completion_tokens');

        if ($content === null || $content === '') {
            $this->logCall($userId, $aiModel, $systemPrompt, $userPrompt, $durationMs,
                inputTokens: $inputTokens,
                outputTokens: $outputTokens,
                error: 'HTTP 200: Response content was null or empty',
            );

            return null;
        }

        $this->logCall($userId, $aiModel, $systemPrompt, $userPrompt, $durationMs,
            response: $content,
            inputTokens: $inputTokens,
            outputTokens: $outputTokens,
            isSuccessful: true,
        );

        return $content;
    }

    private function elapsedMs(int $startTime): int
    {
        return (int) ((hrtime(true) - $startTime) / 1_000_000);
    }

    private function logCall(
        ?string $userId,
        AiModel $aiModel,
        string $systemPrompt,
        string $userPrompt,
        int $durationMs,
        ?string $response = null,
        ?int $inputTokens = null,
        ?int $outputTokens = null,
        bool $isSuccessful = false,
        ?string $error = null,
    ): void {
        if ($userId === null) {
            return;
        }

        AiLog::query()->create([
            'user_id' => $userId,
            'model' => $aiModel->slug,
            'system_prompt' => $systemPrompt,
            'user_prompt' => $userPrompt,
            'response' => $response,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'duration_ms' => $durationMs,
            'is_successful' => $isSuccessful,
            'error' => $error,
        ]);
    }
}
