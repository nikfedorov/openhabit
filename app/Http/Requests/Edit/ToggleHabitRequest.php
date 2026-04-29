<?php

declare(strict_types=1);

namespace App\Http\Requests\Edit;

use App\Http\Concerns\AuthorizesHabitAccess;
use Illuminate\Foundation\Http\FormRequest;

final class ToggleHabitRequest extends FormRequest
{
    use AuthorizesHabitAccess;

    /**
     * The user must own the habit. Franklin virtues can only be toggled all at once.
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
