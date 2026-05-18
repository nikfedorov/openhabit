<?php

declare(strict_types=1);

use App\Enums\SettingType;
use App\Models\Setting;

it('inserts the default system prompt when none exists', function (): void {
    $this->artisan('app:update-ai-system-prompt')
        ->assertSuccessful();

    $setting = Setting::query()->where('key', 'ai_system_prompt')->sole();

    expect($setting->value)->toContain('personal habit-tracking assistant')
        ->and($setting->type)->toBe(SettingType::Markdown);
});

it('overwrites an existing system prompt after confirmation', function (): void {
    Setting::setValue('ai_system_prompt', 'old prompt', SettingType::Markdown);

    $this->artisan('app:update-ai-system-prompt')
        ->expectsConfirmation('This will overwrite the existing AI system prompt. Continue?', 'yes')
        ->assertSuccessful();

    expect(Setting::getValue('ai_system_prompt'))->toContain('personal habit-tracking assistant');
});

it('aborts when user declines overwrite confirmation', function (): void {
    Setting::setValue('ai_system_prompt', 'old prompt', SettingType::Markdown);

    $this->artisan('app:update-ai-system-prompt')
        ->expectsConfirmation('This will overwrite the existing AI system prompt. Continue?', 'no')
        ->assertFailed();

    expect(Setting::getValue('ai_system_prompt'))->toBe('old prompt');
});

it('overwrites without confirmation when --force is passed', function (): void {
    Setting::setValue('ai_system_prompt', 'old prompt', SettingType::Markdown);

    $this->artisan('app:update-ai-system-prompt', ['--force' => true])
        ->assertSuccessful();

    expect(Setting::getValue('ai_system_prompt'))->toContain('personal habit-tracking assistant');
});
