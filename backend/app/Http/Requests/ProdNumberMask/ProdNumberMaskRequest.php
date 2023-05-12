<?php

namespace App\Http\Requests\ProdNumberMask;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProdNumberMaskRequest extends FormRequest
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
        $modelId = request()->route('prod_number_mask')?->id ?? 0;
        return [
            'prod_number' => 'required',
            'mask' => ['required',Rule::unique("prod_number_masks",'mask')->ignore($modelId), function (string $attribute, mixed $value, $fail) {
                if (!str_ends_with($value, "%")) {
                    $fail("The {$attribute} should end with %.");
                }
            },]
        ];
    }
}
