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

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json->has('navigationTranslations')
            ->has('settings')
            ->has('translations')
            ->has('templates')
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

it('returns all user habits including inactive and franklin virtues, excluding other users, in sort order', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $habit2 = Habit::factory()->daily()->create(['user_id' => $user->id, 'is_active' => true, 'sort_order' => 2]);
    $habit1 = Habit::factory()->daily()->create(['user_id' => $user->id, 'is_active' => false, 'sort_order' => 1]);
    Habit::factory()->daily()->franklinVirtue()->create(['user_id' => $user->id, 'sort_order' => 3]);
    Habit::factory()->daily()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('data', 3)
            ->where('data.0.id', $habit1->id)
            ->where('data.1.id', $habit2->id)
            ->etc()
        );
});

it('only returns active templates from active categories that show in templates', function (): void {
    $user = User::factory()->create();
    $activeCategory = CategoryTemplate::factory()->create(['sort_order' => 1]);

    // Valid template — should be returned.
    $visible = HabitTemplate::factory()->daily()->create([
        'category_template_id' => $activeCategory->id,
        'show_in_templates' => true,
        'sort_order' => 1,
    ]);

    // Template from inactive category — excluded.
    $inactiveCategory = CategoryTemplate::factory()->inactive()->create();
    HabitTemplate::factory()->daily()->create([
        'category_template_id' => $inactiveCategory->id,
        'show_in_templates' => true,
    ]);

    // Hidden template — excluded.
    HabitTemplate::factory()->daily()->create([
        'category_template_id' => $activeCategory->id,
        'show_in_templates' => false,
    ]);

    // Inactive template — excluded.
    HabitTemplate::factory()->inactive()->create([
        'category_template_id' => $activeCategory->id,
        'show_in_templates' => true,
    ]);

    // Active category with no templates — no effect.
    CategoryTemplate::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('templates', 1, fn (AssertableJson $json): AssertableJson => $json
                ->where('id', $visible->id)
                ->has('name')
                ->has('human_readable')
                ->has('iterations_required')
                ->has('category')
                ->has('sort_order')
            )
            ->etc()
        );
});

it('orders templates by category sort_order then template sort_order', function (): void {
    $user = User::factory()->create();
    $category2 = CategoryTemplate::factory()->create(['name' => ['en' => 'Second'], 'sort_order' => 2]);
    $category1 = CategoryTemplate::factory()->create(['name' => ['en' => 'First'], 'sort_order' => 1]);

    HabitTemplate::factory()->daily()->create([
        'category_template_id' => $category1->id,
        'name' => ['en' => 'Template A'],
        'show_in_templates' => true,
        'sort_order' => 1,
    ]);
    HabitTemplate::factory()->daily()->create([
        'category_template_id' => $category2->id,
        'name' => ['en' => 'Template B'],
        'show_in_templates' => true,
        'sort_order' => 1,
    ]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/edit')
        ->assertOk()
        ->assertJson(fn (AssertableJson $json): AssertableJson => $json
            ->has('templates', 2)
            ->where('templates.0.name', 'Template A')
            ->where('templates.0.category', 'First')
            ->where('templates.1.name', 'Template B')
            ->where('templates.1.category', 'Second')
            ->etc()
        );
});
