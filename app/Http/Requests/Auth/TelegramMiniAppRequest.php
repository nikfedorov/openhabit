<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Validator;
use SergiX44\Nutgram\Exception\InvalidDataException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Web\WebAppData;
use SergiX44\Nutgram\Telegram\Web\WebAppUser;
use Symfony\Component\HttpFoundation\Response;

final class TelegramMiniAppRequest extends FormRequest
{
    private ?WebAppData $webAppData = null;

    /**
     * @return array<string, string|null>
     */
    public function validationData(): array
    {
        return [
            'init_data' => $this->header('X-Telegram-Init-Data'),
        ];
    }

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'init_data' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'init_data.required' => 'Missing init data',
            'init_data.string' => 'Missing init data',
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

                /** @var string $initData */
                $initData = $this->header('X-Telegram-Init-Data');

                try {
                    $this->webAppData = resolve(Nutgram::class)->validateWebAppData($initData);
                } catch (InvalidDataException) {
                    $validator->errors()->add('init_data', 'Invalid init data');

                    return;
                }

                if (! $this->webAppData->user instanceof WebAppUser) {
                    $validator->errors()->add('init_data', 'User data not found');
                }
            },
        ];
    }

    public function webAppUser(): WebAppUser
    {
        /** @var WebAppData $webAppData */
        $webAppData = $this->webAppData;

        /** @var WebAppUser $user */
        $user = $webAppData->user;

        return $user;
    }

    protected function failedValidation(ValidatorContract $validator): void
    {
        $firstError = $validator->errors()->first();

        throw new HttpResponseException(
            response()->json(
                ['error' => $firstError],
                Response::HTTP_UNAUTHORIZED,
            ),
        );
    }
}
