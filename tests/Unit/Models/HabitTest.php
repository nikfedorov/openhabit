<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\HabitNotification;
use App\Models\User;

test('belongs to user', function (): void {
    $habit = Habit::factory()->create();

    expect($habit->user)->toBeInstanceOf(User::class);
});

test('belongs to category', function (): void {
    $category = Category::factory()->create();
    $habit = Habit::factory()->for($category)->create();

    expect($habit->category)->toBeInstanceOf(Category::class);
});

test('has many completions', function (): void {
    $habit = Habit::factory()->create();
    HabitCompletion::factory()->for($habit)->create();

    expect($habit->completions)->toHaveCount(1);
});

test('has many notifications', function (): void {
    $habit = Habit::factory()->create();
    HabitNotification::factory()->for($habit)->create();

    expect($habit->notifications)->toHaveCount(1);
});

test('isFranklinVirtue returns true when category is franklin virtues', function (): void {
    $category = Category::factory()->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);
    $habit = Habit::factory()->for($category)->create();

    expect($habit->isFranklinVirtue())->toBeTrue();
});

test('isFranklinVirtue returns false without matching category', function (): void {
    $habit = Habit::factory()->create(['category_id' => null]);

    expect($habit->isFranklinVirtue())->toBeFalse();
});

test('getTranslation falls back to any available locale', function (): void {
    $habit = Habit::factory()->create(['name' => ['fr' => 'Habitude']]);

    expect($habit->getTranslation('name', 'de'))->toBe('Habitude');
});

test('getTranslation returns empty fallback when all translations empty', function (): void {
    $habit = Habit::factory()->create(['name' => ['en' => '']]);

    expect($habit->getTranslation('name', 'de'))->toBe('');
});

test('getTranslation returns normal translation when available', function (): void {
    $habit = Habit::factory()->create();
    $habit->setTranslation('name', 'en', 'My Habit');
    $habit->save();

    expect($habit->getTranslation('name', 'en'))->toBe('My Habit');
});

test('delete force deletes when no completions exist', function (): void {
    $habit = Habit::factory()->create();

    $habit->delete();

    expect(Habit::withTrashed()->find($habit->id))->toBeNull();
});

test('delete soft deletes when completions exist', function (): void {
    $habit = Habit::factory()->create();
    HabitCompletion::factory()->for($habit)->create();

    $habit->delete();

    expect(Habit::withTrashed()->find($habit->id))->not->toBeNull()
        ->and(Habit::query()->find($habit->id))->toBeNull();
});

test('ordered scope orders by sort_order', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create(['sort_order' => 2]);
    Habit::factory()->for($user)->create(['sort_order' => 1]);

    $habits = Habit::query()->ordered()->get();

    expect($habits->first()->sort_order)->toBe(1);
});

test('franklinVirtues scope filters correctly', function (): void {
    $category = Category::factory()->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);
    Habit::factory()->for($category)->create();
    Habit::factory()->create(['category_id' => null]);

    expect(Habit::query()->franklinVirtues()->count())->toBe(1);
});

test('excludingFranklinVirtues scope filters correctly', function (): void {
    $category = Category::factory()->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);
    Habit::factory()->for($category)->create();
    Habit::factory()->create(['category_id' => null]);

    expect(Habit::query()->excludingFranklinVirtues()->count())->toBe(1);
});

test('activeOrCompletedDuring scope includes active and completed habits', function (): void {
    $user = User::factory()->create();
    $active = Habit::factory()->for($user)->create(['is_active' => true]);
    $deleted = Habit::factory()->for($user)->create(['is_active' => false]);
    HabitCompletion::factory()->for($deleted)->for($user)->create(['completed_at' => now()]);
    $deleted->delete();

    $count = Habit::query()
        ->where('user_id', $user->id)
        ->activeOrCompletedDuring(now()->subDay(), now()->addDay())
        ->count();

    expect($count)->toBe(2);
});

test('franklinVirtuesFirst scope orders correctly', function (): void {
    $user = User::factory()->create();
    $franklinCategory = Category::factory()->for($user)->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);
    $otherCategory = Category::factory()->for($user)->create(['slug' => 'other']);

    Habit::factory()->for($user)->for($otherCategory)->create();
    Habit::factory()->for($user)->for($franklinCategory)->create();

    $habits = Habit::query()->where('user_id', $user->id)->franklinVirtuesFirst()->get();

    expect($habits->first()->category_id)->toBe($franklinCategory->id);
});

test('casts are correct', function (): void {
    $habit = Habit::factory()->create();

    expect($habit->id)->toBeInt()
        ->and($habit->user_id)->toBeString()
        ->and($habit->is_active)->toBeBool()
        ->and($habit->sort_order)->toBeInt()
        ->and($habit->iterations_required)->toBeInt();
});
