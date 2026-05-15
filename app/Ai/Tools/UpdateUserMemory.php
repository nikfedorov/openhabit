<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Enums\MemoryCategory;
use App\Models\User;
use App\Models\UserMemory;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

/**
 * Updates a single user memory cell (category → content).
 *
 * The agent calls this once per category it wants to refresh. The value
 * fully replaces any existing content for that category.
 */
final readonly class UpdateUserMemory implements Tool
{
    public function __construct(private User $user) {}

    public function description(): string
    {
        return "Update one of the user's memory cells. Call once per category you want to refresh. The provided content fully replaces the previous value for that category. Valid categories: "
            .implode(', ', array_column(MemoryCategory::cases(), 'value'));
    }

    public function handle(Request $request): string
    {
        /** @var string $rawCategory */
        $rawCategory = $request['category'] ?? '';
        /** @var string $content */
        $content = $request['content'] ?? '';

        $category = MemoryCategory::tryFrom($rawCategory);

        if ($category === null) {
            return sprintf(
                'Error: unknown category "%s". Valid categories: %s.',
                $rawCategory,
                implode(', ', array_column(MemoryCategory::cases(), 'value')),
            );
        }

        $content = mb_trim($content);

        if ($content === '') {
            return 'Error: content must not be empty.';
        }

        UserMemory::query()->updateOrCreate(
            ['user_id' => $this->user->id, 'category' => $category],
            ['content' => $content],
        );

        return sprintf('Memory "%s" updated.', $category->value);
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'category' => $schema->string()
                ->enum(array_column(MemoryCategory::cases(), 'value'))
                ->description('Memory cell to update.')
                ->required(),
            'content' => $schema->string()
                ->description('Full replacement content for this memory cell (1-3 sentences).')
                ->required(),
        ];
    }
}
