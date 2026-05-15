<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AiModel;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Ai\Enums\Lab;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\select;
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
            validate: fn (string $value): ?string => AiModel::query()->where('slug', $value)->exists()
                ? sprintf('Slug "%s" is already taken.', $value)
                : null,
        );

        /** @var array<string, string> $providerOptions */
        $providerOptions = collect(Lab::cases())
            ->mapWithKeys(fn (Lab $lab): array => [$lab->value => $lab->value])
            ->all();

        info('Only providers configured in config/ai.php with valid credentials will work. Selecting an unconfigured provider may fail later during digest generation.');

        /** @var string $provider */
        $provider = select(
            label: 'Provider (must be configured in config/ai.php)',
            options: $providerOptions,
            default: Lab::OpenRouter->value,
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
            'provider' => Lab::from($provider),
            'priority' => $priority,
            'is_active' => true,
        ]);

        info(sprintf('✓ AI model "%s" added successfully.', $name));

        return self::SUCCESS;
    }
}
