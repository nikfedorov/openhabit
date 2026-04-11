<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Habit;
use App\Models\User;

it('belongs to user', function (): void {
    $category = Category::factory()->create();

    expect($category->user)->toBeInstanceOf(User::class);
});

it('has many habits', function (): void {
    $category = Category::factory()->create();
    Habit::factory()->for($category)->create();

    expect($category->habits)->toHaveCount(1);
});

it('returns true for matching slug on isFranklinVirtues', function (): void {
    $category = Category::factory()->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);

    expect($category->isFranklinVirtues())->toBeTrue();
});

it('returns false for other slug on isFranklinVirtues', function (): void {
    $category = Category::factory()->create(['slug' => 'other']);

    expect($category->isFranklinVirtues())->toBeFalse();
});

it('filters correctly with active scope', function (): void {
    Category::factory()->create(['is_active' => true]);
    Category::factory()->create(['is_active' => false]);

    expect(Category::query()->active()->count())->toBe(1);
});

it('filters correctly with excludingFranklinVirtues scope', function (): void {
    Category::factory()->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);
    Category::factory()->create(['slug' => 'other']);

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
