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
            ['slug' => 'qwen/qwen3-next-80b-a3b-instruct:free', 'name' => 'Qwen 3 Next 80B'],

            ['slug' => 'meta-llama/llama-3.3-70b-instruct:free', 'name' => 'Llama 3.3 70B'],
            ['slug' => 'minimax/minimax-m2.5:free', 'name' => 'Minimax M2.5'],

            ['slug' => 'openai/gpt-oss-120b:free', 'name' => 'GPT OSS 120B'],
            ['slug' => 'openai/gpt-oss-20b:free', 'name' => 'GPT OSS 20B'],

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
