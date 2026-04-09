<?php

declare(strict_types=1);

namespace App\Http\Controllers\View;

use App\Actions\View\IndexAction;
use App\Http\Requests\View\IndexRequest;
use App\Http\Resources\ViewResource;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

final readonly class IndexController
{
    public function __construct(private IndexAction $viewAction) {}

    public function show(IndexRequest $request, #[CurrentUser] User $user): ViewResource
    {
        return new ViewResource($this->viewAction->handle(
            $user,
            $request->selectedTab(),
            $request->selectedWeek(),
            $request->selectedYear(),
        ));
    }
}
