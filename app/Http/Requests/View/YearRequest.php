<?php

declare(strict_types=1);

namespace App\Http\Requests\View;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class YearRequest extends FormRequest
{
    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            /**
             * Year index (age) for the year grid (0–79).
             *
             * @example 25
             */
            'year' => ['nullable', 'integer', 'min:0', 'max:79'],
        ];
    }

    public function selectedYear(): ?int
    {
        /** @var int|null $year */
        $year = $this->safe()['year'] ?? null;

        return $year !== null ? (int) $year : null;
    }
}
