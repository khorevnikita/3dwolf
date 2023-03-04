<?php

namespace App\Http\Requests\Estimate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstimateRequest extends FormRequest
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
        $modelId = request()->route('estimate')?->id ?? 0;
        return [
            'name' => ['required', Rule::unique('estimates', 'name')->ignore($modelId)]
        ];
    }
}
