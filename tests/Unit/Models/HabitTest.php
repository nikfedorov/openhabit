<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\HabitNotification;
use App\Models\User;

it('belongs to user', function (): void {
    $habit = Habit::factory()->create();

    expect($habit->user)->toBeInstanceOf(User::class);
});

it('belongs to category', function (): void {
    $category = Category::factory()->create();
    $habit = Habit::factory()->for($category)->create();

    expect($habit->category)->toBeInstanceOf(Category::class);
});

it('has many completions', function (): void {
    $habit = Habit::factory()->create();
    HabitCompletion::factory()->for($habit)->create();

    expect($habit->completions)->toHaveCount(1);
});

it('has many notifications', function (): void {
    $habit = Habit::factory()->create();
    HabitNotification::factory()->for($habit)->create();

    expect($habit->notifications)->toHaveCount(1);
});

it('returns true when category is franklin virtues', function (): void {
    $category = Category::factory()->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);
    $habit = Habit::factory()->for($category)->create();

    expect($habit->isFranklinVirtue())->toBeTrue();
});

it('returns false without matching category', function (): void {
    $habit = Habit::factory()->create(['category_id' => null]);

    expect($habit->isFranklinVirtue())->toBeFalse();
});

it('falls back to any available locale on getTranslation', function (): void {
    $habit = Habit::factory()->create(['name' => ['fr' => 'Habitude']]);

    expect($habit->getTranslation('name', 'de'))->toBe('Habitude');
});

it('returns empty fallback when all translations empty on getTranslation', function (): void {
    $habit = Habit::factory()->create(['name' => ['en' => '']]);

    expect($habit->getTranslation('name', 'de'))->toBe('');
});

it('returns normal translation when available on getTranslation', function (): void {
    $habit = Habit::factory()->create();
    $habit->setTranslation('name', 'en', 'My Habit');
    $habit->save();

    expect($habit->getTranslation('name', 'en'))->toBe('My Habit');
});

it('force deletes when no completions exist', function (): void {
    $habit = Habit::factory()->create();

    $habit->delete();

    expect(Habit::withTrashed()->find($habit->id))->toBeNull();
});

it('soft deletes when completions exist', function (): void {
    $habit = Habit::factory()->create();
    HabitCompletion::factory()->for($habit)->create();

    $habit->delete();

    expect(Habit::withTrashed()->find($habit->id))->not->toBeNull()
        ->and(Habit::query()->find($habit->id))->toBeNull();
});

it('orders correctly with ordered scope', function (): void {
    $user = User::factory()->create();
    Habit::factory()->for($user)->create(['sort_order' => 2]);
    Habit::factory()->for($user)->create(['sort_order' => 1]);

    $habits = Habit::query()->ordered()->get();

    expect($habits->first()->sort_order)->toBe(1);
});

it('filters correctly with franklinVirtues scope', function (): void {
    $category = Category::factory()->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);
    Habit::factory()->for($category)->create();
    Habit::factory()->create(['category_id' => null]);

    expect(Habit::query()->franklinVirtues()->count())->toBe(1);
});

it('filters correctly with excludingFranklinVirtues scope', function (): void {
    $category = Category::factory()->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);
    Habit::factory()->for($category)->create();
    Habit::factory()->create(['category_id' => null]);

    expect(Habit::query()->excludingFranklinVirtues()->count())->toBe(1);
});

it('includes active and completed habits with activeOrCompletedDuring scope', function (): void {
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

it('orders correctly with franklinVirtuesFirst scope', function (): void {
    $user = User::factory()->create();
    $franklinCategory = Category::factory()->for($user)->create(['slug' => Category::FRANKLIN_VIRTUES_SLUG]);
    $otherCategory = Category::factory()->for($user)->create(['slug' => 'other']);

    Habit::factory()->for($user)->for($otherCategory)->create();
    Habit::factory()->for($user)->for($franklinCategory)->create();

    $habits = Habit::query()->where('user_id', $user->id)->franklinVirtuesFirst()->get();

    expect($habits->first()->category_id)->toBe($franklinCategory->id);
});

it('has correct casts', function (): void {
    $habit = Habit::factory()->create();

    expect($habit->id)->toBeInt()
        ->and($habit->user_id)->toBeString()
        ->and($habit->is_active)->toBeBool()
        ->and($habit->sort_order)->toBeInt()
        ->and($habit->iterations_required)->toBeInt();
});
