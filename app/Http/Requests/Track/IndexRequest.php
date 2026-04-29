<?php

declare(strict_types=1);

namespace App\Http\Requests\Track;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class IndexRequest extends FormRequest
{
    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            /**
             * Date to retrieve tracking data for. Defaults to today.
             *
             * @example "2026-04-06"
             */
            'date' => ['nullable', 'date'],
        ];
    }

    public function selectedDate(): ?string
    {
        $date = $this->safe()->string('date')->value();

        return $date !== '' ? $date : null;
    }
}
