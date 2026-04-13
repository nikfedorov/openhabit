<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Habit;
use App\Models\User;

it('has correct relationships', function (): void {
    $category = Category::factory()->create();
    Habit::factory()->for($category)->create();

    expect($category->user)->toBeInstanceOf(User::class)
        ->and($category->habits)->toHaveCount(1);
});

it('detects franklin virtues slug', function (): void {
    $franklin = Category::factory()->franklinVirtues()->create();
    $other = Category::factory()->create(['slug' => 'other']);

    expect($franklin->is_franklin_virtues)->toBeTrue()
        ->and($other->is_franklin_virtues)->toBeFalse();
});

it('filters correctly with active scope', function (): void {
    Category::factory()->create(['is_active' => true]);
    Category::factory()->create(['is_active' => false]);

    expect(Category::query()->active()->count())->toBe(1);
});

it('filters correctly with excludingFranklinVirtues scope', function (): void {
    Category::factory()->franklinVirtues()->create();
    Category::factory()->create();

    expect(Category::query()->excludingFranklinVirtues()->count())->toBe(1);
});

it('orders correctly with ordered scope', function (): void {
    Category::factory()->create(['sort_order' => 2]);
    Category::factory()->create(['sort_order' => 1]);

    $categories = Category::query()->ordered()->get();

    expect($categories->first()->sort_order)->toBe(1);
});

it('has correct casts', function (): void {
    $category = Category::factory()->create();

    expect($category->id)->toBeInt()
        ->and($category->user_id)->toBeString()
        ->and($category->is_active)->toBeBool()
        ->and($category->sort_order)->toBeInt();
});
