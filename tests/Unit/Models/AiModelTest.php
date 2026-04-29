<?php

declare(strict_types=1);

use App\Models\AiModel;

it('returns active models by priority on getOrdered', function (): void {
    AiModel::factory()->create(['is_active' => true, 'priority' => 2]);
    AiModel::factory()->create(['is_active' => true, 'priority' => 1]);
    AiModel::factory()->create(['is_active' => false, 'priority' => 0]);

    $models = AiModel::getOrdered();

    expect($models)->toHaveCount(2)
        ->and($models->first()->priority)->toBe(1);
});

it('has correct casts', function (): void {
    $model = AiModel::factory()->create();

    expect($model->id)->toBeInt()
        ->and($model->priority)->toBeInt()
        ->and($model->is_active)->toBeBool();
});
