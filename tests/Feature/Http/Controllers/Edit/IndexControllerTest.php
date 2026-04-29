<?php

declare(strict_types=1);

use App\Actions\Edit\ReorderHabitsAction;
use App\Models\CategoryTemplate;
use App\Models\Habit;
use App\Models\HabitTemplate;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

it('requires authentication', function (): void {
    $this->getJson('/api/edit')
        ->assertUnauthorized();
});

it('returns habits and translations', function (): void {
    $user = User::factory()->create();
    $habit = Habit::factory()->daily()->create(['user_id' => $user->id, 'sort_order' => 1]);
    $category = CategoryTemplate::factory()->create(['sort_order' => 1]);
    HabitTemplate::factory()->daily()->create([
        'category_template_id' => $category->id,
        'show_in_templates' => true,
        'sort_order' => 1,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('navigationTranslations')
            ->has('settings')
            ->has('translations')
            ->has('templates', 1, fn (AssertableJson $json): AssertableJson => $json
                ->has('id')
                ->has('name')
                ->has('human_readable')
                ->has('iterations_required')
                ->has('category')
                ->has('sort_order')
            )
            ->where('minSortOrder', ReorderHabitsAction::MIN_SORT_ORDER)
            ->has('data', 1, fn (AssertableJson $json): AssertableJson => $json
                ->where('id', $habit->id)
                ->has('name')
                ->has('description')
                ->has('is_active')
                ->has('sort_order')
                ->has('iterations_required')
                ->has('human_readable')
                ->has('is_franklin_virtue')
            )
        );
});
