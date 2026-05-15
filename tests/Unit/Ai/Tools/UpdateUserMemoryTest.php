<?php

declare(strict_types=1);

use App\Ai\Tools\UpdateUserMemory;
use App\Enums\MemoryCategory;
use App\Models\User;
use App\Models\UserMemory;
use Illuminate\JsonSchema\JsonSchemaTypeFactory;
use Laravel\Ai\Tools\Request;

it('creates a new memory cell for the user', function (): void {
    $user = User::factory()->create();
    $tool = new UpdateUserMemory($user);

    $result = $tool->handle(new Request([
        'category' => MemoryCategory::LongTerm->value,
        'content' => 'User is consistent with reading.',
    ]));

    expect($result)->toBe('Memory "long_term" updated.');

    $memory = UserMemory::query()->where('user_id', $user->id)->sole();
    expect($memory->category)->toBe(MemoryCategory::LongTerm)
        ->and($memory->content)->toBe('User is consistent with reading.');
});

it('replaces existing content for the same category', function (): void {
    $user = User::factory()->create();
    UserMemory::factory()->for($user)->create([
        'category' => MemoryCategory::Goals,
        'content' => 'Old goals.',
    ]);

    new UpdateUserMemory($user)->handle(new Request([
        'category' => MemoryCategory::Goals->value,
        'content' => 'New goals.',
    ]));

    expect(UserMemory::query()->where('user_id', $user->id)->where('category', MemoryCategory::Goals)->sole()->content)
        ->toBe('New goals.');
});

it('returns an error for an unknown category', function (): void {
    $user = User::factory()->create();

    $result = new UpdateUserMemory($user)->handle(new Request([
        'category' => 'unknown',
        'content' => 'irrelevant',
    ]));

    expect($result)->toStartWith('Error: unknown category');
    expect(UserMemory::query()->where('user_id', $user->id)->count())->toBe(0);
});

it('returns an error for empty content', function (): void {
    $user = User::factory()->create();

    $result = new UpdateUserMemory($user)->handle(new Request([
        'category' => MemoryCategory::Goals->value,
        'content' => '   ',
    ]));

    expect($result)->toBe('Error: content must not be empty.');
    expect(UserMemory::query()->where('user_id', $user->id)->count())->toBe(0);
});

it('exposes a description and a schema with category and content fields', function (): void {
    $tool = new UpdateUserMemory(User::factory()->create());

    expect($tool->description())->toContain('memory');

    $schema = $tool->schema(new JsonSchemaTypeFactory);
    expect($schema)->toHaveKeys(['category', 'content']);
});
