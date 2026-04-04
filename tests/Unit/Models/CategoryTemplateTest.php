<?php

declare(strict_types=1);

use App\Models\CategoryTemplate;
use App\Models\HabitTemplate;

test('toCategoryArray returns correct structure', function (): void {
    $template = CategoryTemplate::factory()->create([
        'name' => 'Test',
        'slug' => 'test-slug',
    ]);

    expect($template->toCategoryArray())->toBe([
        'name' => 'Test',
        'slug' => 'test-slug',
    ]);
});

test('has many habit templates', function (): void {
    $template = CategoryTemplate::factory()->create();
    HabitTemplate::factory()->for($template)->create();

    expect($template->habitTemplates)->toHaveCount(1);
});

test('active scope filters correctly', function (): void {
    CategoryTemplate::factory()->create(['is_active' => true]);
    CategoryTemplate::factory()->create(['is_active' => false]);

    expect(CategoryTemplate::query()->active()->count())->toBe(1);
});

test('copyByDefault scope filters correctly', function (): void {
    CategoryTemplate::factory()->create(['copy_by_default' => true]);
    CategoryTemplate::factory()->create(['copy_by_default' => false]);

    expect(CategoryTemplate::query()->copyByDefault()->count())->toBe(1);
});

test('ordered scope orders by sort_order', function (): void {
    CategoryTemplate::factory()->create(['sort_order' => 2]);
    CategoryTemplate::factory()->create(['sort_order' => 1]);

    $templates = CategoryTemplate::query()->ordered()->get();

    expect($templates->first()->sort_order)->toBe(1);
});

test('casts are correct', function (): void {
    $template = CategoryTemplate::factory()->create();

    expect($template->id)->toBeInt()
        ->and($template->is_active)->toBeBool()
        ->and($template->copy_by_default)->toBeBool()
        ->and($template->sort_order)->toBeInt();
});
