<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Habit;
use App\Models\User;

test('belongs to user', function (): void {
    $category = Category::factory()->create();

    expect($category->user)->toBeInstanceOf(User::class);
});

test('has many habits', function (): void {
    $category = Category::factory()->create();
    Habit::factory()->for($category)->create();

    expect($category->habits)->toHaveCount(1);
});

test('isFranklinVirtues returns true for matching slug', function (): void {
    $category = Category::factory()->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);

    expect($category->isFranklinVirtues())->toBeTrue();
});

test('isFranklinVirtues returns false for other slug', function (): void {
    $category = Category::factory()->create(['slug' => 'other']);

    expect($category->isFranklinVirtues())->toBeFalse();
});

test('active scope filters active categories', function (): void {
    Category::factory()->create(['is_active' => true]);
    Category::factory()->create(['is_active' => false]);

    expect(Category::query()->active()->count())->toBe(1);
});

test('excludingFranklinVirtues scope filters correctly', function (): void {
    Category::factory()->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);
    Category::factory()->create(['slug' => 'other']);

    expect(Category::query()->excludingFranklinVirtues()->count())->toBe(1);
});

test('ordered scope orders by sort_order', function (): void {
    Category::factory()->create(['sort_order' => 2]);
    Category::factory()->create(['sort_order' => 1]);

    $categories = Category::query()->ordered()->get();

    expect($categories->first()->sort_order)->toBe(1);
});

test('casts are correct', function (): void {
    $category = Category::factory()->create();

    expect($category->id)->toBeInt()
        ->and($category->user_id)->toBeString()
        ->and($category->is_active)->toBeBool()
        ->and($category->sort_order)->toBeInt();
});
