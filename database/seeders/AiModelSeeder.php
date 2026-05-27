<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiModel;
use Illuminate\Database\Seeder;
use Laravel\Ai\Enums\Lab;

final class AiModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            ['slug' => 'deepseek/deepseek-v3.2', 'name' => 'DeepSeek: DeepSeek V3.2'],
        ];

        foreach ($models as $i => $model) {
            AiModel::query()->updateOrCreate(
                ['slug' => $model['slug']],
                [
                    'provider' => Lab::OpenRouter,
                    'is_active' => true,
                    'priority' => $i + 1,
                    ...$model,
                ],
            );
        }
    }
}
