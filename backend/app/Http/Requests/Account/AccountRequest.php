<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $modelId = request()->route('account')?->id ?? 0;
        return [
            'name' => ['required', Rule::unique('accounts', 'name')->ignore($modelId)],
            'balance' => ['required', 'numeric'],
            'expected_income' => ['sometimes', 'numeric'],
        ];
    }
}
