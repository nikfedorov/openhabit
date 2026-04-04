<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\AiDigest;
use App\Models\User;

final class GenerateAiDigestAction
{
    /**
     * Generate an AI digest for the given user.
     */
    public function execute(User $user): ?AiDigest
    {
        // TODO: Implement AI digest generation
        return AiDigest::query()
            ->where('user_id', $user->id)
            ->whereDate('date', now()->toDateString())
            ->first();
    }
}
