<?php

declare(strict_types=1);

use App\Models\AiModel;
use Laravel\Ai\Enums\Lab;

it('adds an ai model with the provided fields and auto-generated slug', function (): void {
    $this->artisan('app:add-ai-model')
        ->expectsQuestion('Model name', 'My Custom Model')
        ->expectsQuestion('Slug', 'my-custom-model')
        ->expectsQuestion('Provider (must be configured in config/ai.php)', Lab::OpenAI->value)
        ->expectsQuestion('Priority (lower = higher priority)', '1')
        ->expectsConfirmation('Add model "My Custom Model" (my-custom-model)?', 'yes')
        ->expectsPromptsInfo('✓ AI model "My Custom Model" added successfully.')
        ->assertSuccessful();

    expect(AiModel::query()->where('slug', 'my-custom-model')->sole())
        ->name->toBe('My Custom Model')
        ->provider->toBe(Lab::OpenAI)
        ->priority->toBe(1)
        ->is_active->toBeTrue();
});

it('rejects a duplicate slug', function (): void {
    AiModel::factory()->create(['slug' => 'gpt-4o']);

    $this->artisan('app:add-ai-model')
        ->expectsQuestion('Model name', 'GPT-4o')
        ->expectsQuestion('Slug', 'gpt-4o')
        ->expectsOutputToContain('Slug "gpt-4o" is already taken.')
        ->assertFailed();
});

it('aborts when user declines confirmation', function (): void {
    $this->artisan('app:add-ai-model')
        ->expectsQuestion('Model name', 'GPT-4o')
        ->expectsQuestion('Slug', 'gpt-4o')
        ->expectsQuestion('Provider (must be configured in config/ai.php)', Lab::OpenRouter->value)
        ->expectsQuestion('Priority (lower = higher priority)', '0')
        ->expectsConfirmation('Add model "GPT-4o" (gpt-4o)?', 'no')
        ->expectsPromptsInfo('Aborted.')
        ->assertSuccessful();

    expect(AiModel::query()->where('slug', 'gpt-4o')->exists())->toBeFalse();
});
