<?php

declare(strict_types=1);

use App\Models\CategoryTemplate;
use App\Models\HabitTemplate;

test('toHabitArray returns correct structure', function (): void {
    $template = HabitTemplate::factory()->create([
        'name' => ['en' => 'Test Habit'],
        'description' => ['en' => 'A description'],
        'sort_order' => 5,
        'rrule' => 'FREQ=DAILY',
        'iterations_required' => 3,
    ]);

    expect($template->toHabitArray())->toBe([
        'name' => ['en' => 'Test Habit'],
        'description' => ['en' => 'A description'],
        'sort_order' => 5,
        'rrule' => 'FREQ=DAILY',
        'iterations_required' => 3,
    ]);
});

test('belongs to category template', function (): void {
    $categoryTemplate = CategoryTemplate::factory()->create();
    $template = HabitTemplate::factory()->for($categoryTemplate)->create();

    expect($template->categoryTemplate)->toBeInstanceOf(CategoryTemplate::class);
});

test('active scope filters correctly', function (): void {
    HabitTemplate::factory()->create(['is_active' => true]);
    HabitTemplate::factory()->create(['is_active' => false]);

    expect(HabitTemplate::query()->active()->count())->toBe(1);
});

test('copyByDefault scope filters correctly', function (): void {
    HabitTemplate::factory()->create(['copy_by_default' => true]);
    HabitTemplate::factory()->create(['copy_by_default' => false]);

    expect(HabitTemplate::query()->copyByDefault()->count())->toBe(1);
});

test('showInTemplates scope filters correctly', function (): void {
    HabitTemplate::factory()->create(['show_in_templates' => true]);
    HabitTemplate::factory()->create(['show_in_templates' => false]);

    expect(HabitTemplate::query()->showInTemplates()->count())->toBe(1);
});

test('ordered scope orders by sort_order', function (): void {
    HabitTemplate::factory()->create(['sort_order' => 2]);
    HabitTemplate::factory()->create(['sort_order' => 1]);

    $templates = HabitTemplate::query()->ordered()->get();

    expect($templates->first()->sort_order)->toBe(1);
});

test('casts are correct', function (): void {
    $categoryTemplate = CategoryTemplate::factory()->create();
    $template = HabitTemplate::factory()->for($categoryTemplate)->create();

    expect($template->id)->toBeInt()
        ->and($template->category_template_id)->toBeInt()
        ->and($template->iterations_required)->toBeInt()
        ->and($template->is_active)->toBeBool()
        ->and($template->copy_by_default)->toBeBool()
        ->and($template->show_in_templates)->toBeBool()
        ->and($template->sort_order)->toBeInt();
});
