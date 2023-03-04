<?php

namespace App\Http\Requests\Manufacturer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManufacturerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        $modelId = request()->route('manufacturer')?->id ?? 0;
        return [
            'name' => ['required', Rule::unique('manufacturers', 'name')->ignore($modelId)]
        ];
    }
}
