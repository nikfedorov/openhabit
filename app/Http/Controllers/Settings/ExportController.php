<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\Settings\ExportUserDataAction;
use App\Http\Requests\Settings\ExportRequest;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;

/**
 * Trigger data export and return a signed download URL.
 */
#[Group('Settings', weight: 2)]
final readonly class ExportController
{
    /**
     * Export user data and return a signed URL for download.
     */
    public function store(
        ExportRequest $request,
        #[CurrentUser] User $user,
        ExportUserDataAction $exportUserData,
    ): JsonResponse {

        $path = $exportUserData->handle($user);

        return response()->json([
            /**
             * The signed URL pointing to the file download route.
             */
            'url' => URL::signedRoute('export.download', [
                'filename' => basename($path),
            ]),
        ]);
    }
}
