<?php

declare(strict_types=1);

use App\Models\AiModel;

test('getOrdered returns active models by priority', function (): void {
    AiModel::factory()->create(['is_active' => true, 'priority' => 2]);
    AiModel::factory()->create(['is_active' => true, 'priority' => 1]);
    AiModel::factory()->create(['is_active' => false, 'priority' => 0]);

    $models = AiModel::getOrdered();

    expect($models)->toHaveCount(2)
        ->and($models->first()->priority)->toBe(1);
});

test('casts are correct', function (): void {
    $model = AiModel::factory()->create();

    expect($model->id)->toBeInt()
        ->and($model->priority)->toBeInt()
        ->and($model->is_active)->toBeBool()
        ->and($model->is_free)->toBeBool();
});
