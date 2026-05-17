<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\SettingType;
use App\Models\Setting;
use Database\Seeders\SettingSeeder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;

#[Signature('app:update-ai-system-prompt')]
#[Description('Reset the AI system prompt to the default value from SettingSeeder')]
final class UpdateAiSystemPromptCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $current = Setting::getValue('ai_system_prompt');

        if ($current !== null && ! confirm('This will overwrite the existing AI system prompt. Continue?')) {
            return self::FAILURE;
        }

        Setting::setValue('ai_system_prompt', SettingSeeder::defaultSystemPrompt(), SettingType::Markdown);

        info('AI system prompt updated successfully.');

        return self::SUCCESS;
    }
}
