<?php

namespace App\Http\Requests\v0\Operator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\v0\Operator\Enums\OperatorType;

class StoreOperatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|unique:operators,name|max:255',
            'name_alias' => 'nullable|string|max:255',
            'operator_type' => ['nullable', Rule::enum(OperatorType::class)],
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'logo_url' => 'nullable|url|max:255',
            'profile' => 'nullable|string',
            'active' => 'nullable|boolean',
        ];
    }
}
