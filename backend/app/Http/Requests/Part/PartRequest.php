<?php

namespace App\Http\Requests\Part;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartRequest extends FormRequest
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
        $modelId = request()->route('part')?->id ?? 0;
        $inv_num = request()->input("inv_number");
        $isMask = str_contains("%", $inv_num);
        $count = request()->input('count');
        return [
            'bought_at' => 'required|date',
            'inv_number' => ['required', $isMask && $count ? '' : Rule::unique('parts', 'inv_number')->ignore($modelId)],
            'count' => ['sometimes', 'integer', 'min:1'],
            'prod_number' => 'sometimes',
            'manufacturer_id' => 'required|integer|exists:manufacturers,id',
            'material_id' => 'required|integer|exists:materials,id',
            'color' => 'sometimes',
            'weight' => 'numeric',
            'price' => 'numeric',
            'status' => 'required|in:new,opened,ended'
        ];
    }
}
