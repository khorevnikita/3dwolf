<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderLineRequest extends FormRequest
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
       # $modelId = request()->route('order_line')?->id ?? 0;
        return [
           # "order_id" => "required|integer|exists:orders,id",
         #   "number" => ['required', Rule::unique('order_lines', 'number')->ignore($modelId)],
            "name" => "required",
            "weight" => "sometimes|numeric",
            "print_duration" => "sometimes|numeric",
            "part_id" => "required|integer|exists:parts,id",
            "count" => "required|integer",
            "price" => "required|numeric",
        ];
    }
}
