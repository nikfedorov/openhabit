<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Ai\Support\DigestResult;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

/**
 * Commit tool: delivers the final digest text and ends the agent's turn.
 *
 * Contract follows draft-vs-commit discipline from the agents-best-practices
 * skill: memory updates are drafts that can run autonomously, while this tool
 * is the single commit point. The harness — not the prompt — enforces the
 * length cap, the non-emptiness rule, and idempotency.
 */
final readonly class TaskDone implements Tool
{
    /** Hard cap enforced by the harness — matches the digest rule in the system prompt. */
    public const int MAX_DIGEST_CHARS = 500;

    public function __construct(private DigestResult $result) {}

    public function description(): string
    {
        return <<<'TXT'
Deliver the final digest and finish the turn.

Use when: all relevant memory updates are done and the digest text is ready.
Do not use: more than once per turn, or to send drafts/partial results.
Side effect: ends the agent's turn; further tool calls will be rejected.
Errors: returns a structured `error:` observation if the digest is empty, too
long, or if this tool was already called. On error, fix and call again.
TXT;
    }

    public function handle(Request $request): string
    {
        /** @var string $digest */
        $digest = $request['digest'] ?? '';
        $digest = mb_trim($digest);

        if ($digest === '') {
            return 'error: empty_digest. next: produce a non-empty digest text and call task_done again.';
        }

        $length = mb_strlen($digest);
        if ($length > self::MAX_DIGEST_CHARS) {
            return sprintf(
                'error: digest_too_long (%d chars, max %d). next: shorten the digest and call task_done again.',
                $length,
                self::MAX_DIGEST_CHARS,
            );
        }

        if ($this->result->digest !== null) {
            return 'error: already_called. next: do not call task_done again; your previous digest has been recorded.';
        }

        $this->result->digest = $digest;

        return 'ok: digest_recorded. next: stop. do not emit further tool calls or text.';
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'digest' => $schema->string()
                ->min(1)
                ->max(self::MAX_DIGEST_CHARS)
                ->description('Final plain-text digest. Max '.self::MAX_DIGEST_CHARS.' characters. No markdown, HTML, or emoji.')
                ->required(),
        ];
    }
}
