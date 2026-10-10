<?php

namespace App\Http\Requests\v0\Asset;

use App\Traits\ApiResponse;
use App\Traits\Errors;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;
use App\Modules\v0\Customers\Models\Customer;

class StoreWithdrawalRequest extends FormRequest
{
    use ApiResponse, Errors;

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            self::error(
                self::$badRequest,
                $validator->errors()->toArray(),
                422
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
            'asset_wallet_id' => [
                'required',
                'uuid',
                Rule::exists('asset_wallets', 'wallet_reference')
                    ->where('owner_type', Customer::class)
                    ->where('owner_id', optional($this->user())->getAuthIdentifier())
                    ->where('status', 'active'),
            ],
            'amount' => 'required|numeric|min:0.00000001',
            'conversion_type' => 'nullable|in:gold_to_cash',
            'notes' => 'nullable|string|max:2000',
        ];
    }
}
