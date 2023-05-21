<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
        $modelId = auth("sanctum")->id();
        return [
            'name' => 'required|max:255',
            'surname' => 'required|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($modelId)],
        ];
    }
}
