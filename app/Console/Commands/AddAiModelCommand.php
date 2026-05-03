<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AiModel;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('app:add-ai-model')]
#[Description('Add a new AI model to the database')]
final class AddAiModelCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = text(
            label: 'Model name',
            placeholder: 'e.g. GPT-4o',
            required: true,
        );

        $slug = text(
            label: 'Slug',
            default: Str::slug($name),
            required: true,
        );

        $baseUrl = text(
            label: 'Base URL',
            placeholder: 'e.g. https://api.openai.com/v1',
            required: true,
        );

        $apiKey = password(
            label: 'API key (leave empty to skip)',
        );

        $priority = (int) text(
            label: 'Priority (lower = higher priority)',
            default: '0',
            required: true,
            validate: fn (string $value): ?string => is_numeric($value) ? null : 'Priority must be a number.',
        );

        if (! confirm(sprintf('Add model "%s" (%s)?', $name, $slug), default: true)) {
            info('Aborted.');

            return self::SUCCESS;
        }

        AiModel::query()->create([
            'name' => $name,
            'slug' => $slug,
            'base_url' => $baseUrl,
            'api_key' => $apiKey !== '' ? $apiKey : null,
            'priority' => $priority,
            'is_active' => true,
        ]);

        info(sprintf('✓ AI model "%s" added successfully.', $name));

        return self::SUCCESS;
    }
}
