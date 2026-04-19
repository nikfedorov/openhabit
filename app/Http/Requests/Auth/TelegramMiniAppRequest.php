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
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            /**
             * The init data from Telegram Web App.
             *
             * @example user_id=123456789&auth_date=1697040000&hash=abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234567890
             */
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
                $initData = $this->input('init_data');

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
