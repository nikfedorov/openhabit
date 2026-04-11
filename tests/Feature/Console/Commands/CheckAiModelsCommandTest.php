<?php

declare(strict_types=1);

use App\Models\AiModel;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

it('fails when api key is not configured', function (): void {
    config(['services.openrouter.api_key' => null]);

    $this->artisan('app:check-ai-models')
        ->expectsOutputToContain('OpenRouter API key is not configured')
        ->assertExitCode(1);
});

it('fails when api key is empty string', function (): void {
    config(['services.openrouter.api_key' => '']);

    $this->artisan('app:check-ai-models')
        ->expectsOutputToContain('OpenRouter API key is not configured')
        ->assertExitCode(1);
});

it('succeeds when no free models exist', function (): void {
    config(['services.openrouter.api_key' => 'test-key']);

    $this->artisan('app:check-ai-models')
        ->expectsOutputToContain('No free AI models to check')
        ->assertExitCode(0);
});

it('enables inactive model when health check passes', function (): void {
    config(['services.openrouter.api_key' => 'test-key']);
    config(['services.openrouter.base_url' => 'https://openrouter.test/api/v1']);

    $model = AiModel::factory()->free()->inactive()->create();

    Http::fake([
        'openrouter.test/*' => Http::response([
            'choices' => [['message' => ['content' => 'Hello']]],
        ]),
    ]);

    $this->artisan('app:check-ai-models')
        ->assertExitCode(0);

    expect($model->refresh()->is_active)->toBeTrue();
});

it('disables active model when health check fails', function (): void {
    config(['services.openrouter.api_key' => 'test-key']);
    config(['services.openrouter.base_url' => 'https://openrouter.test/api/v1']);

    $model = AiModel::factory()->free()->create(['is_active' => true]);

    Http::fake([
        'openrouter.test/*' => Http::response(['error' => 'model down'], 500),
    ]);

    $this->artisan('app:check-ai-models')
        ->assertExitCode(0);

    expect($model->refresh()->is_active)->toBeFalse();
});

it('handles connection exception gracefully', function (): void {
    config(['services.openrouter.api_key' => 'test-key']);
    config(['services.openrouter.base_url' => 'https://openrouter.test/api/v1']);

    $model = AiModel::factory()->free()->create(['is_active' => true]);

    Http::fake([
        'openrouter.test/*' => fn () => throw new ConnectionException('timeout'),
    ]);

    $this->artisan('app:check-ai-models')
        ->assertExitCode(0);

    expect($model->refresh()->is_active)->toBeFalse();
});

it('keeps already active model when health check passes', function (): void {
    config(['services.openrouter.api_key' => 'test-key']);
    config(['services.openrouter.base_url' => 'https://openrouter.test/api/v1']);

    $model = AiModel::factory()->free()->create(['is_active' => true]);

    Http::fake([
        'openrouter.test/*' => Http::response([
            'choices' => [['message' => ['content' => 'Hello']]],
        ]),
    ]);

    $this->artisan('app:check-ai-models')
        ->expectsOutputToContain('Enabled: 0, Disabled: 0')
        ->assertExitCode(0);

    expect($model->refresh()->is_active)->toBeTrue();
});

it('response without content is treated as unhealthy', function (): void {
    config(['services.openrouter.api_key' => 'test-key']);
    config(['services.openrouter.base_url' => 'https://openrouter.test/api/v1']);

    $model = AiModel::factory()->free()->create(['is_active' => true]);

    Http::fake([
        'openrouter.test/*' => Http::response([
            'choices' => [['message' => ['content' => null]]],
        ]),
    ]);

    $this->artisan('app:check-ai-models')
        ->assertExitCode(0);

    expect($model->refresh()->is_active)->toBeFalse();
});
