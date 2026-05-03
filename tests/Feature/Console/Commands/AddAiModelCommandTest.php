<?php

declare(strict_types=1);

use App\Models\AiModel;

it('adds an ai model with all fields', function (): void {
    $this->artisan('app:add-ai-model')
        ->expectsQuestion('Model name', 'GPT-4o')
        ->expectsQuestion('Slug', 'gpt-4o')
        ->expectsQuestion('Base URL', 'https://api.openai.com/v1')
        ->expectsQuestion('API key (leave empty to skip)', 'sk-secret')
        ->expectsQuestion('Priority (lower = higher priority)', '1')
        ->expectsConfirmation('Add model "GPT-4o" (gpt-4o)?', 'yes')
        ->expectsPromptsInfo('✓ AI model "GPT-4o" added successfully.')
        ->assertSuccessful();

    expect(AiModel::query()->where('slug', 'gpt-4o')->first())
        ->not->toBeNull()
        ->name->toBe('GPT-4o')
        ->base_url->toBe('https://api.openai.com/v1')
        ->priority->toBe(1)
        ->is_active->toBeTrue();
});

it('adds an ai model without an api key', function (): void {
    $this->artisan('app:add-ai-model')
        ->expectsQuestion('Model name', 'Gemini Pro')
        ->expectsQuestion('Slug', 'gemini-pro')
        ->expectsQuestion('Base URL', 'https://generativelanguage.googleapis.com/v1')
        ->expectsQuestion('API key (leave empty to skip)', '')
        ->expectsQuestion('Priority (lower = higher priority)', '0')
        ->expectsConfirmation('Add model "Gemini Pro" (gemini-pro)?', 'yes')
        ->expectsPromptsInfo('✓ AI model "Gemini Pro" added successfully.')
        ->assertSuccessful();

    expect(AiModel::query()->where('slug', 'gemini-pro')->first())
        ->not->toBeNull()
        ->api_key->toBeNull();
});

it('auto-generates slug from name', function (): void {
    $this->artisan('app:add-ai-model')
        ->expectsQuestion('Model name', 'My Custom Model')
        ->expectsQuestion('Slug', 'my-custom-model')
        ->expectsQuestion('Base URL', 'https://api.example.com/v1')
        ->expectsQuestion('API key (leave empty to skip)', '')
        ->expectsQuestion('Priority (lower = higher priority)', '0')
        ->expectsConfirmation('Add model "My Custom Model" (my-custom-model)?', 'yes')
        ->assertSuccessful();

    expect(AiModel::query()->where('slug', 'my-custom-model')->exists())->toBeTrue();
});

it('aborts when user declines confirmation', function (): void {
    $this->artisan('app:add-ai-model')
        ->expectsQuestion('Model name', 'GPT-4o')
        ->expectsQuestion('Slug', 'gpt-4o')
        ->expectsQuestion('Base URL', 'https://api.openai.com/v1')
        ->expectsQuestion('API key (leave empty to skip)', '')
        ->expectsQuestion('Priority (lower = higher priority)', '0')
        ->expectsConfirmation('Add model "GPT-4o" (gpt-4o)?', 'no')
        ->expectsPromptsInfo('Aborted.')
        ->assertSuccessful();

    expect(AiModel::query()->where('slug', 'gpt-4o')->exists())->toBeFalse();
});
