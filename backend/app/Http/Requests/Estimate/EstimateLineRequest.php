<?php

namespace App\Http\Requests\Estimate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstimateLineRequest extends FormRequest
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
        $modelId = request()->route('estimate_line')?->id ?? 0;
        return [
            'key' => ['required', Rule::unique('estimate_lines', 'key')->ignore($modelId)],
            'name' => ['required'],
            'price' => ['required', 'numeric', 'min:0'],
            'count' => ['required', 'numeric', 'min:1'],
            'weight' => ['required', 'numeric', 'min:0'],
            'print_duration' => ['required'],
        ];
    }
}
