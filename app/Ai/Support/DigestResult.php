<?php

declare(strict_types=1);

namespace App\Ai\Support;

/**
 * Mutable shared state populated by the digest agent's TaskDone tool.
 *
 * The agent does not return structured output. Instead, its TaskDone tool
 * writes the final digest text here, where the calling action picks it up.
 */
final class DigestResult
{
    public ?string $digest = null;
}
