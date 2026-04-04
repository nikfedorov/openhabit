<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AiModel;
use Illuminate\Database\Seeder;

final class AiModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            ['slug' => 'nousresearch/hermes-3-llama-3.1-405b:free', 'name' => 'Hermes 3 405B', 'priority' => 1, 'is_active' => true, 'is_free' => true],
            ['slug' => 'stepfun/step-3.5-flash:free', 'name' => 'Step 3.5 Flash', 'priority' => 2, 'is_active' => true, 'is_free' => true],
            ['slug' => 'z-ai/glm-4.5-air:free', 'name' => 'GLM 4.5 Air', 'priority' => 3, 'is_active' => true, 'is_free' => true],
            ['slug' => 'minimax/minimax-m2.5:free', 'name' => 'Minimax M2.5', 'priority' => 4, 'is_active' => true, 'is_free' => true],
            ['slug' => 'sourceful/riverflow-v2-fast-preview', 'name' => 'Riverflow V2 Fast', 'priority' => 5, 'is_active' => true, 'is_free' => true],
            ['slug' => 'openai/gpt-oss-120b:free', 'name' => 'GPT OSS 120B', 'priority' => 6, 'is_active' => true, 'is_free' => true],
            ['slug' => 'openai/gpt-oss-20b:free', 'name' => 'GPT OSS 20B', 'priority' => 7, 'is_active' => true, 'is_free' => true],
            ['slug' => 'liquid/lfm-2.5-1.2b-thinking:free', 'name' => 'LFM 2.5 Thinking', 'priority' => 8, 'is_active' => true, 'is_free' => true],
            ['slug' => 'liquid/lfm-2.5-1.2b-instruct:free', 'name' => 'LFM 2.5 Instruct', 'priority' => 9, 'is_active' => true, 'is_free' => true],
            ['slug' => 'arcee-ai/trinity-large-preview:free', 'name' => 'Trinity Large', 'priority' => 10, 'is_active' => true, 'is_free' => true],
            ['slug' => 'arcee-ai/trinity-mini:free', 'name' => 'Trinity Mini', 'priority' => 11, 'is_active' => true, 'is_free' => true],
            ['slug' => 'google/gemma-3-27b-it:free', 'name' => 'Gemma 3 27B', 'priority' => 12, 'is_active' => true, 'is_free' => true],
            ['slug' => 'google/gemma-3-12b-it:free', 'name' => 'Gemma 3 12B', 'priority' => 13, 'is_active' => true, 'is_free' => true],
            ['slug' => 'google/gemma-3-4b-it:free', 'name' => 'Gemma 3 4B', 'priority' => 14, 'is_active' => true, 'is_free' => true],
            ['slug' => 'google/lyria-3-pro-preview', 'name' => 'Lyria 3 Pro', 'priority' => 15, 'is_active' => true, 'is_free' => true],
            ['slug' => 'google/lyria-3-clip-preview', 'name' => 'Lyria 3 Clip', 'priority' => 16, 'is_active' => true, 'is_free' => true],
            ['slug' => 'meta-llama/llama-3.3-70b-instruct:free', 'name' => 'Llama 3.3 70B', 'priority' => 17, 'is_active' => true, 'is_free' => true],
            ['slug' => 'meta-llama/llama-3.2-3b-instruct:free', 'name' => 'Llama 3.2 3B', 'priority' => 18, 'is_active' => true, 'is_free' => true],
            ['slug' => 'qwen/qwen3-next-80b-a3b-instruct:free', 'name' => 'Qwen 3 Next 80B', 'priority' => 19, 'is_active' => true, 'is_free' => true],
            ['slug' => 'qwen/qwen3.6-plus-preview:free', 'name' => 'Qwen 3.6 Plus', 'priority' => 20, 'is_active' => true, 'is_free' => true],
            ['slug' => 'nvidia/nemotron-3-super-120b-a12b:free', 'name' => 'Nemotron 3 Super 120B', 'priority' => 21, 'is_active' => true, 'is_free' => true],
            ['slug' => 'nvidia/nemotron-3-nano-30b-a3b:free', 'name' => 'Nemotron 3 Nano 30B', 'priority' => 22, 'is_active' => true, 'is_free' => true],
            ['slug' => 'nvidia/nemotron-nano-12b-v2-vl:free', 'name' => 'Nemotron Nano 12B VL', 'priority' => 23, 'is_active' => true, 'is_free' => true],
            ['slug' => 'nvidia/nemotron-nano-9b-v2:free', 'name' => 'Nemotron Nano 9B', 'priority' => 24, 'is_active' => true, 'is_free' => true],
        ];

        foreach ($models as $model) {
            AiModel::query()->updateOrCreate(
                ['slug' => $model['slug']],
                $model,
            );
        }
    }
}
