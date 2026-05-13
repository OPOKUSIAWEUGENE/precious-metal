<?php

namespace App\Http\Requests\v0\Auth\PlatformManager;

use App\Traits\ApiResponse;
use App\Traits\Errors;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
{
    use ApiResponse, Errors;

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            self::error(
                self::$badRequest,
                $validator->errors()->all(),
                400
            )
        );
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /**
             * @example admin@neutek.local
             */
            'username' => 'required|string|max:255',
            /**
             * @example password123
             */
            'password' => 'required|string|max:255',
        ];
    }
}
