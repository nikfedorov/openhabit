<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Support\DigestResult;
use App\Ai\Tools\TaskDone;
use App\Ai\Tools\UpdateUserMemory;
use App\Models\User;
use App\Services\AiPromptService;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;

/**
 * Agent that produces the daily AI digest for a user.
 *
 * The agent receives the user's habit data + daily note as the prompt,
 * uses the UpdateUserMemory tool to refresh memory cells, and signals
 * completion via the TaskDone tool which carries the final digest text.
 */
#[Timeout(60)]
final class DailyDigestAgent implements Agent, HasTools
{
    use Promptable;
    use RemembersConversations;

    public function __construct(
        public readonly User $user,
        public readonly DigestResult $result,
        private readonly AiPromptService $promptService,
    ) {}

    public function instructions(): string
    {
        return $this->promptService->buildSystemPrompt($this->user);
    }

    /**
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new UpdateUserMemory($this->user),
            new TaskDone($this->result),
        ];
    }
}
