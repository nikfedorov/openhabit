<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\UploadStoryImageAction;
use App\Http\Requests\StoryUploadRequest;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

/**
 * Handles story image uploads for Telegram story sharing.
 */
#[Group('Story')]
final readonly class StoryShareController
{
    public function __construct(private UploadStoryImageAction $uploadStory) {}

    /**
     * Upload story image.
     */
    public function __invoke(StoryUploadRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        $url = $this->uploadStory->handle($user, $request->decodedImage());

        return response()->json([
            /**
             * URL of the uploaded story image to be shared to Telegram.
             *
             * @example https://localhost/storage/stories/123.png
             */
            'url' => $url,
        ]);
    }
}
