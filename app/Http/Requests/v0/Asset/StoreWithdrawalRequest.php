<?php

namespace App\Http\Requests\v0\Asset;

use App\Traits\ApiResponse;
use App\Traits\Errors;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreWithdrawalRequest extends FormRequest
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
            'asset_wallet_id' => 'required|uuid|exists:asset_wallets,id',
            'amount' => 'required|numeric|min:0.00000001',
            'conversion_type' => 'nullable|in:gold_to_cash',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
