<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $modelId = request()->route('user')?->id ?? 0;
        return [
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($modelId)],
            'balance' => ['required_without:customer_id', 'numeric'],
            'password' => $modelId === 0 ? ['required', 'confirmed', 'min:6'] : [],
            'access_level' => ['required', 'integer', 'in:0,1,2']
        ];
    }
}
