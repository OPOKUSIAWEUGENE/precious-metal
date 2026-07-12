<?php

namespace App\Http\Requests\v0\Fee;

use App\Traits\ApiResponse;
use App\Traits\Errors;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFeeRequest extends FormRequest
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
            'fee' => 'required|numeric|min:0',
            'effective_at' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
