<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

final class AiModelSeeder extends Seeder
{
    public function run(): void
    {
        $openRouterBaseUrl = Config::string('services.openrouter.base_url', 'https://openrouter.ai/api/v1');
        $openRouterApiKey = Config::string('services.openrouter.api_key');

        $models = [
            ['slug' => 'z-ai/glm-4.5-air:free', 'name' => 'GLM 4.5 Air'],
            ['slug' => 'minimax/minimax-m2.5:free', 'name' => 'Minimax M2.5'],

            ['slug' => 'openai/gpt-oss-120b:free', 'name' => 'GPT OSS 120B'],
            ['slug' => 'openai/gpt-oss-20b:free', 'name' => 'GPT OSS 20B'],

            ['slug' => 'google/gemma-3-27b-it:free', 'name' => 'Gemma 3 27B'],
            ['slug' => 'google/gemma-3-12b-it:free', 'name' => 'Gemma 3 12B'],
            ['slug' => 'google/gemma-3-4b-it:free', 'name' => 'Gemma 3 4B'],

            ['slug' => 'meta-llama/llama-3.3-70b-instruct:free', 'name' => 'Llama 3.3 70B'],
            ['slug' => 'meta-llama/llama-3.2-3b-instruct:free', 'name' => 'Llama 3.2 3B'],

            ['slug' => 'qwen/qwen3-next-80b-a3b-instruct:free', 'name' => 'Qwen 3 Next 80B'],
            ['slug' => 'deepseek/deepseek-v3.2', 'name' => 'DeepSeek: DeepSeek V3.2'],
            ['slug' => 'tencent/hy3-preview:free', 'name' => 'Tencent: Hy3 preview'],
        ];

        foreach ($models as $i => $model) {
            AiModel::query()->updateOrCreate(
                ['slug' => $model['slug']],
                [
                    'base_url' => $openRouterBaseUrl,
                    'api_key' => $openRouterApiKey,
                    'is_active' => true,
                    'priority' => $i + 1,
                    ...$model,
                ],
            );
        }
    }
}
