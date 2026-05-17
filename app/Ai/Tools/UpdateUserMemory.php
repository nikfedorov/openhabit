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
 * Draft-class tool: updates a single user memory cell (category → content).
 *
 * Each call replaces the previous content for one category. Calling twice
 * for the same category in one turn overwrites the earlier value and the
 * harness returns a structured warning so the agent can correct course.
 */
final class UpdateUserMemory implements Tool
{
    /** Reasonable upper bound for a 1-3 sentence memory cell. */
    public const int MAX_CONTENT_CHARS = 600;

    /**
     * Categories already updated in this turn — used to flag duplicates.
     *
     * @var array<string, true>
     */
    private array $updatedCategories = [];

    public function __construct(private readonly User $user) {}

    public function description(): string
    {
        $valid = implode(', ', array_column(MemoryCategory::cases(), 'value'));

        return <<<TXT
Refresh one of the user's long-term memory cells. Fully replaces the previous
content for the given category.

Use when: you have a meaningful, durable update (1-3 concise sentences).
Do not use: for trivia, restating yesterday's digest, or to clear a category
without a replacement. Skip categories that did not change meaningfully.
Side effect: persists immediately. The change is visible to the next turn.
Errors: returns `error:` for unknown category or empty/too-long content;
returns `ok` with `warning: duplicate_category` if the same category is
updated twice in one turn (last write wins).

Valid categories: {$valid}.
Max content length: {$this->maxLen()} characters.
TXT;
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
                'error: unknown_category "%s". next: choose one of: %s.',
                $rawCategory,
                implode(', ', array_column(MemoryCategory::cases(), 'value')),
            );
        }

        $content = mb_trim($content);

        if ($content === '') {
            return 'error: empty_content. next: provide 1-3 concise sentences or skip this category.';
        }

        $length = mb_strlen($content);
        if ($length > self::MAX_CONTENT_CHARS) {
            return sprintf(
                'error: content_too_long (%d chars, max %d). next: shorten and retry.',
                $length,
                self::MAX_CONTENT_CHARS,
            );
        }

        UserMemory::query()->updateOrCreate(
            ['user_id' => $this->user->id, 'category' => $category],
            ['content' => $content],
        );

        $duplicate = isset($this->updatedCategories[$category->value]);
        $this->updatedCategories[$category->value] = true;

        if ($duplicate) {
            return sprintf(
                'ok: memory "%s" updated. warning: duplicate_category — previous value overwritten. next: do not update this category again.',
                $category->value,
            );
        }

        return sprintf(
            'ok: memory "%s" updated. next: update another category if needed, then call task_done.',
            $category->value,
        );
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
                ->min(1)
                ->max(self::MAX_CONTENT_CHARS)
                ->description('Full replacement content for this memory cell (1-3 sentences, max '.self::MAX_CONTENT_CHARS.' characters).')
                ->required(),
        ];
    }

    private function maxLen(): int
    {
        return self::MAX_CONTENT_CHARS;
    }
}
