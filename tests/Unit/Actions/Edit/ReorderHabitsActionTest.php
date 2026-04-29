<?php

declare(strict_types=1);

use App\Actions\Edit\ReorderHabitsAction;
use App\Models\Habit;
use App\Models\User;

it('updates sort_order using MIN_SORT_ORDER as the base and ignores foreign or franklin habit ids', function (): void {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $first = Habit::factory()->daily()->for($user)->create(['sort_order' => 200]);
    $second = Habit::factory()->daily()->for($user)->create(['sort_order' => 201]);
    $franklin = Habit::factory()->daily()->franklinVirtue()->for($user)->create(['sort_order' => 50]);
    $foreign = Habit::factory()->daily()->for($other)->create(['sort_order' => 999]);

    resolve(ReorderHabitsAction::class)->handle($user, [
        $second->id, $first->id, $franklin->id, $foreign->id,
    ]);

    expect($second->fresh()->sort_order)->toBe(ReorderHabitsAction::MIN_SORT_ORDER)
        ->and($first->fresh()->sort_order)->toBe(ReorderHabitsAction::MIN_SORT_ORDER + 1)
        ->and($franklin->fresh()->sort_order)->toBe(50)
        ->and($foreign->fresh()->sort_order)->toBe(999);
});

it('does nothing when none of the provided ids belong to the user regular habits', function (): void {
    $user = User::factory()->create();
    $franklin = Habit::factory()->daily()->franklinVirtue()->for($user)->create(['sort_order' => 7]);

    resolve(ReorderHabitsAction::class)->handle($user, [$franklin->id, 999_999]);

    expect($franklin->fresh()->sort_order)->toBe(7);
});
