<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

final class StoryUploadRequest extends FormRequest
{
    private const string DATA_URL_PREFIX = 'data:image/png;base64,';

    private string $decodedImage = '';

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'image' => ['required', 'string', 'starts_with:'.self::DATA_URL_PREFIX],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $base64 = Str::after($this->string('image')->toString(), self::DATA_URL_PREFIX);
                $decoded = base64_decode($base64, true);

                if ($decoded === false) {
                    $validator->errors()->add('image', 'Invalid base64 data.');

                    return;
                }

                $this->decodedImage = $decoded;
            },
        ];
    }

    public function decodedImage(): string
    {
        return $this->decodedImage;
    }
}
