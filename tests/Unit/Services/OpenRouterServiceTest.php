<?php

declare(strict_types=1);

use App\Models\AiLog;
use App\Models\AiModel;
use App\Models\User;
use App\Services\OpenRouterService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Helper: call the service under test.
 */
function generate(?string $userId = null): ?string
{
    return new OpenRouterService()->generate('system', 'user', $userId);
}

/**
 * Helper: fake a single successful response.
 *
 * @param  array<string, mixed>  $extra  Extra body fields (e.g. usage tokens).
 */
function fakeAiSuccess(string $content = 'OK', array $extra = []): void
{
    Http::fake([
        'https://openrouter.ai/*' => Http::response([
            'choices' => [['message' => ['content' => $content]]],
            ...$extra,
        ]),
    ]);
}

beforeEach(function (): void {
    config()->set('services.openrouter.base_url', 'https://openrouter.ai/api/v1');
    config()->set('services.openrouter.api_key', 'test-key');
    AiModel::factory()->create(['slug' => 'test-model', 'priority' => 1]);
});

test('returns null and warns when API key is missing', function (): void {
    config()->set('services.openrouter.api_key');
    Log::shouldReceive('warning')->once()->with('OpenRouter API key is not configured');

    expect(generate())->toBeNull();
});

test('returns null and warns when no active models configured', function (): void {
    AiModel::query()->delete();
    AiModel::factory()->inactive()->create();
    Log::shouldReceive('warning')->once()->with('No active AI models configured');

    expect(generate())->toBeNull();
});

test('returns content and logs success on 200 with usage', function (): void {
    $user = User::factory()->create();
    fakeAiSuccess('Great response!', ['usage' => ['prompt_tokens' => 100, 'completion_tokens' => 50]]);

    expect(generate($user->id))->toBe('Great response!');

    $log = AiLog::query()->where('user_id', $user->id)->sole();
    expect($log->is_successful)->toBeTrue()
        ->and($log->response)->toBe('Great response!')
        ->and($log->input_tokens)->toBe(100)
        ->and($log->output_tokens)->toBe(50);
});

test('returns null and logs HTTP error', function (): void {
    $user = User::factory()->create();
    Http::fake(['https://openrouter.ai/*' => Http::response(['error' => 'Too many requests'], 429)]);

    expect(generate($user->id))->toBeNull();

    $log = AiLog::query()->where('user_id', $user->id)->sole();
    expect($log->is_successful)->toBeFalse()
        ->and($log->error)->toContain('HTTP 429');
});

test('returns null and logs error on empty content', function (): void {
    $user = User::factory()->create();
    fakeAiSuccess('');

    expect(generate($user->id))->toBeNull();

    $log = AiLog::query()->where('user_id', $user->id)->sole();
    expect($log->is_successful)->toBeFalse()
        ->and($log->error)->toBe('HTTP 200: Response content was null or empty');
});

test('rotates to next model when first fails', function (): void {
    AiModel::query()->delete();
    AiModel::factory()->create(['slug' => 'model-a', 'priority' => 1]);
    AiModel::factory()->create(['slug' => 'model-b', 'priority' => 2]);

    Http::fake([
        'https://openrouter.ai/*' => Http::sequence()
            ->push(['error' => 'server error'], 500)
            ->push(['choices' => [['message' => ['content' => 'From model B']]]]),
    ]);

    expect(generate())->toBe('From model B');
    Http::assertSentCount(2);
});

test('returns null when all models fail', function (): void {
    Http::fake(['https://openrouter.ai/*' => Http::response(['error' => 'server error'], 500)]);

    expect(generate())->toBeNull();
});

test('logs connection exception', function (): void {
    $user = User::factory()->create();
    Http::fake(['https://openrouter.ai/*' => fn () => throw new ConnectionException('Connection timed out')]);

    expect(generate($user->id))->toBeNull();

    $log = AiLog::query()->where('user_id', $user->id)->sole();
    expect($log->is_successful)->toBeFalse()
        ->and($log->error)->toBe('Connection timed out');
});

test('does not log when userId is null', function (): void {
    fakeAiSuccess('Response');

    generate();

    expect(AiLog::query()->count())->toBe(0);
});
