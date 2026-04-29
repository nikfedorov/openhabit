<?php

declare(strict_types=1);

namespace App\Http\Requests\Edit;

use Illuminate\Foundation\Http\FormRequest;

final class ReorderHabitsRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'ordered_ids' => ['required', 'array'],
            'ordered_ids.*' => ['integer'],
        ];
    }

    /**
     * @return array<int>
     */
    public function orderedIds(): array
    {
        /** @var array<int> */
        return $this->validated('ordered_ids');
    }
}
