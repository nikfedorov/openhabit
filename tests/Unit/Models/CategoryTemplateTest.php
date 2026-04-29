<?php

declare(strict_types=1);

use App\Models\CategoryTemplate;
use App\Models\HabitTemplate;

it('returns correct structure on toCategoryArray', function (): void {
    $template = CategoryTemplate::factory()->create([
        'name' => ['en' => 'Test'],
        'description' => ['en' => 'A description'],
        'slug' => 'test-slug',
        'sort_order' => 3,
    ]);

    expect($template->toCategoryArray())->toBe([
        'name' => ['en' => 'Test'],
        'description' => ['en' => 'A description'],
        'slug' => 'test-slug',
        'sort_order' => 3,
    ]);
});

it('has many habit templates', function (): void {
    $template = CategoryTemplate::factory()->create();
    HabitTemplate::factory()->for($template)->create();

    expect($template->habitTemplates)->toHaveCount(1);
});

it('filters correctly with active scope', function (): void {
    CategoryTemplate::factory()->create(['is_active' => true]);
    CategoryTemplate::factory()->create(['is_active' => false]);

    expect(CategoryTemplate::query()->active()->count())->toBe(1);
});

it('filters correctly with copyByDefault scope', function (): void {
    CategoryTemplate::factory()->create(['copy_by_default' => true]);
    CategoryTemplate::factory()->create(['copy_by_default' => false]);

    expect(CategoryTemplate::query()->copyByDefault()->count())->toBe(1);
});

it('orders correctly with ordered scope', function (): void {
    CategoryTemplate::factory()->create(['sort_order' => 2]);
    CategoryTemplate::factory()->create(['sort_order' => 1]);

    $templates = CategoryTemplate::query()->ordered()->get();

    expect($templates->first()->sort_order)->toBe(1);
});

it('has correct casts', function (): void {
    $template = CategoryTemplate::factory()->create();

    expect($template->id)->toBeInt()
        ->and($template->is_active)->toBeBool()
        ->and($template->copy_by_default)->toBeBool()
        ->and($template->sort_order)->toBeInt();
});
