<?php

declare(strict_types=1);

use App\Actions\Edit\GetUserHabitsAction;
use App\Models\Category;
use App\Models\Habit;
use App\Models\User;

it('returns the given user habits ordered with category eager-loaded', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $category = Category::factory()->for($user)->create();

    $second = Habit::factory()->daily()->for($user)->create(['sort_order' => 2, 'category_id' => $category->id]);
    $first = Habit::factory()->daily()->for($user)->create(['sort_order' => 1]);
    Habit::factory()->daily()->for($other)->create();

    $habits = resolve(GetUserHabitsAction::class)->handle($user);

    expect($habits)->toHaveCount(2)
        ->and($habits[0]->id)->toBe($first->id)
        ->and($habits[1]->id)->toBe($second->id)
        ->and($habits[1]->relationLoaded('category'))->toBeTrue()
        ->and($habits[1]->category?->id)->toBe($category->id);
});
