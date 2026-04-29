<?php

declare(strict_types=1);

namespace App\Http\Requests\Edit;

use App\Http\Concerns\AuthorizesHabitAccess;
use Illuminate\Foundation\Http\FormRequest;

final class DestroyHabitRequest extends FormRequest
{
    use AuthorizesHabitAccess;

    /**
     * The user must own the habit and it must not be a Franklin virtue.
     */
    public function authorize(): bool
    {
        return $this->authorizeHabitAccess();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
