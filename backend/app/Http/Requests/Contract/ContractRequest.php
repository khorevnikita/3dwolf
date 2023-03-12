<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractRequest extends FormRequest
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
        $modelId = request()->route('contract')?->id ?? 0;
        return [
            'number' => ['required',Rule::unique('contracts', 'number')->ignore($modelId)],
            'date'=>'required|date',
            'customer_id'=>'required|integer|exists:customers,id',
            'status'=>'required|in:new,process,complete',
            'amount'=>'required|numeric'
        ];
    }
}
