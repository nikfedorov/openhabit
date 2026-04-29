<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\HabitNotification;
use App\Models\User;

it('has correct relationships', function (): void {
    $category = Category::factory()->create();
    $habit = Habit::factory()->for($category)->create();
    HabitCompletion::factory()->for($habit)->create();
    HabitNotification::factory()->for($habit)->create();

    expect($habit->user)->toBeInstanceOf(User::class)
        ->and($habit->category)->toBeInstanceOf(Category::class)
        ->and($habit->completions)->toHaveCount(1)
        ->and($habit->notifications)->toHaveCount(1);
});

it('detects franklin virtue category', function (): void {
    $franklin = Habit::factory()->franklinVirtue()->create();
    $regular = Habit::factory()->create(['category_id' => null]);

    expect($franklin->is_franklin_virtue)->toBeTrue()
        ->and($regular->is_franklin_virtue)->toBeFalse();
});

it('handles translation fallback correctly', function (): void {
    $habitFr = Habit::factory()->create(['name' => ['fr' => 'Habitude']]);
    $habitEmpty = Habit::factory()->create(['name' => ['en' => '']]);
    $habitEn = Habit::factory()->create();
    $habitEn->setTranslation('name', 'en', 'My Habit');
    $habitEn->save();

    expect($habitFr->getTranslation('name', 'de'))->toBe('Habitude')
        ->and($habitEmpty->getTranslation('name', 'de'))->toBe('')
        ->and($habitEn->getTranslation('name', 'en'))->toBe('My Habit');
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

it('filters correctly with franklin virtue scopes', function (): void {
    Habit::factory()->franklinVirtue()->create();
    Habit::factory()->create(['category_id' => null]);

    expect(Habit::query()->franklinVirtues()->count())->toBe(1)
        ->and(Habit::query()->excludingFranklinVirtues()->count())->toBe(1);
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
    $otherCategory = Category::factory()->for($user)->create();

    Habit::factory()->for($user)->for($otherCategory)->create();
    $franklinHabit = Habit::factory()->for($user)->franklinVirtue()->create();

    $habits = Habit::query()->where('user_id', $user->id)->franklinVirtuesFirst()->get();

    expect($habits->first()->id)->toBe($franklinHabit->id);
});

it('has correct casts', function (): void {
    $habit = Habit::factory()->create();

    expect($habit->id)->toBeInt()
        ->and($habit->user_id)->toBeInt()
        ->and($habit->is_active)->toBeBool()
        ->and($habit->sort_order)->toBeInt()
        ->and($habit->iterations_required)->toBeInt();
});
