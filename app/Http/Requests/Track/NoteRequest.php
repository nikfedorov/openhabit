<?php

declare(strict_types=1);

namespace App\Http\Requests\Track;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class NoteRequest extends FormRequest
{
    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            /**
             * Date the note belongs to.
             *
             * @example "2026-04-06"
             */
            'date' => ['required', 'date'],

            /**
             * Note text content. Send null to clear.
             *
             * @example "Great day!"
             */
            'content' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function noteDate(): string
    {
        return $this->safe()->string('date')->value();
    }

    public function content(): ?string
    {
        $content = $this->safe()->string('content')->value();

        return $content !== '' ? $content : null;
    }
}
