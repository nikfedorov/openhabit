<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\AiModel;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

final class CheckAiModelsCommand extends Command
{
    private const int TIMEOUT_SECONDS = 15;

    protected $signature = 'app:check-ai-models';

    protected $description = 'Health-check all free AI models and toggle their active status';

    public function handle(): int
    {
        /** @var string|null $apiKey */
        $apiKey = config('services.openrouter.api_key');

        if ($apiKey === '' || $apiKey === null) {
            $this->error('OpenRouter API key is not configured');

            return self::FAILURE;
        }

        /** @var string $baseUrl */
        $baseUrl = config('services.openrouter.base_url');

        $models = AiModel::query()
            ->where('is_free', true)
            ->select(['id', 'slug', 'name', 'is_active'])
            ->get();

        if ($models->isEmpty()) {
            $this->info('No free AI models to check');

            return self::SUCCESS;
        }

        $toEnable = [];
        $toDisable = [];

        foreach ($models as $model) {
            $this->output->write(sprintf('Checking: %s... ', $model->slug), false);
            $isHealthy = $this->checkModel($baseUrl, $apiKey, $model->slug);
            $this->line($isHealthy ? 'OK' : 'FAIL');

            if ($isHealthy && ! $model->is_active) {
                $toEnable[] = $model->id;
                $this->line('Enabled: '.$model->name);
            } elseif (! $isHealthy && $model->is_active) {
                $toDisable[] = $model->id;
                $this->error('Disabled: '.$model->name);
            }
        }

        if ($toEnable !== []) {
            AiModel::query()->whereIn('id', $toEnable)->update(['is_active' => true]);
        }

        if ($toDisable !== []) {
            AiModel::query()->whereIn('id', $toDisable)->update(['is_active' => false]);
        }

        $enabled = count($toEnable);
        $disabled = count($toDisable);

        $this->info(sprintf('Health check complete. Enabled: %d, Disabled: %d', $enabled, $disabled));

        return self::SUCCESS;
    }

    /**
     * Send a minimal request to check if the model responds successfully.
     */
    private function checkModel(string $baseUrl, string $apiKey, string $slug): bool
    {
        try {
            $response = Http::withToken($apiKey)
                ->timeout(self::TIMEOUT_SECONDS)
                ->post($baseUrl.'/chat/completions', [
                    'model' => $slug,
                    'messages' => [
                        ['role' => 'user', 'content' => 'Hi'],
                    ],
                ]);

            return $response->successful() && $response->json('choices.0.message.content') !== null;
        } catch (ConnectionException) {
            return false;
        }
    }
}
