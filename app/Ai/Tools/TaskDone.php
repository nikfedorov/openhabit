<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Ai\Support\DigestResult;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

/**
 * Agent calls this tool to report that work is finished and deliver
 * the final digest text. The agent should call this exactly once at
 * the end of its turn.
 */
final readonly class TaskDone implements Tool
{
    public function __construct(private DigestResult $result) {}

    public function description(): string
    {
        return 'Call once when you are done. Pass the final digest text as the `digest` argument. Do not call this until all memory updates are complete.';
    }

    public function handle(Request $request): string
    {
        /** @var string $digest */
        $digest = $request['digest'] ?? '';

        $digest = mb_trim($digest);

        if ($digest === '') {
            return 'Error: digest must not be empty.';
        }

        $this->result->digest = $digest;

        return 'Done.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'digest' => $schema->string()
                ->description('Final digest text to deliver to the user.')
                ->required(),
        ];
    }
}
