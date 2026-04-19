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
            ['slug' => 'nousresearch/hermes-3-llama-3.1-405b:free', 'name' => 'Hermes 3 405B', 'is_free' => true],
            ['slug' => 'z-ai/glm-4.5-air:free', 'name' => 'GLM 4.5 Air', 'is_free' => true],
            ['slug' => 'minimax/minimax-m2.5:free', 'name' => 'Minimax M2.5', 'is_free' => true],

            ['slug' => 'openai/gpt-oss-120b:free', 'name' => 'GPT OSS 120B', 'is_free' => true],
            ['slug' => 'openai/gpt-oss-20b:free', 'name' => 'GPT OSS 20B', 'is_free' => true],

            ['slug' => 'liquid/lfm-2.5-1.2b-thinking:free', 'name' => 'LFM 2.5 Thinking', 'is_free' => true],
            ['slug' => 'liquid/lfm-2.5-1.2b-instruct:free', 'name' => 'LFM 2.5 Instruct', 'is_free' => true],

            ['slug' => 'arcee-ai/trinity-large-preview:free', 'name' => 'Trinity Large', 'is_free' => true],
            ['slug' => 'arcee-ai/trinity-mini:free', 'name' => 'Trinity Mini', 'is_free' => true],

            ['slug' => 'google/gemma-3-27b-it:free', 'name' => 'Gemma 3 27B', 'is_free' => true],
            ['slug' => 'google/gemma-3-12b-it:free', 'name' => 'Gemma 3 12B', 'is_free' => true],
            ['slug' => 'google/gemma-3-4b-it:free', 'name' => 'Gemma 3 4B', 'is_free' => true],

            ['slug' => 'meta-llama/llama-3.3-70b-instruct:free', 'name' => 'Llama 3.3 70B', 'is_free' => true],
            ['slug' => 'meta-llama/llama-3.2-3b-instruct:free', 'name' => 'Llama 3.2 3B', 'is_free' => true],

            ['slug' => 'qwen/qwen3-next-80b-a3b-instruct:free', 'name' => 'Qwen 3 Next 80B', 'is_free' => true],
            ['slug' => 'qwen/qwen3.6-plus-preview:free', 'name' => 'Qwen 3.6 Plus', 'is_free' => true],

            ['slug' => 'nvidia/nemotron-3-super-120b-a12b:free', 'name' => 'Nemotron 3 Super 120B', 'is_free' => true],
            ['slug' => 'nvidia/nemotron-3-nano-30b-a3b:free', 'name' => 'Nemotron 3 Nano 30B', 'is_free' => true],
            ['slug' => 'nvidia/nemotron-nano-12b-v2-vl:free', 'name' => 'Nemotron Nano 12B VL', 'is_free' => true],
            ['slug' => 'nvidia/nemotron-nano-9b-v2:free', 'name' => 'Nemotron Nano 9B', 'is_free' => true],
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
