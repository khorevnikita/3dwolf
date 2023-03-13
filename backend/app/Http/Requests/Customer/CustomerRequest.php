<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRequest extends FormRequest
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
        $modelId = request()->route('customer')?->id ?? 0;
        return [
            'name' => 'required',
            'surname' => 'required',
            'father_name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'telegram' => 'required',
            'type' => 'required|in:individual,entity',
            'entity_type' => 'required_if:type,entity|in:self_employed,company',
            'title' => 'required_if:type,entity',
            'inn' => 'required_if:type,entity',
            'kpp' => 'required_if:entity_type,company',
            'ogrn' => 'required_if:type,entity',
            'okpo' => 'required_if:type,entity',
            'okved' => 'required_if:type,entity',
            'address' => 'required_if:entity_type,self_employed',
            'ceo' => 'required_if:entity_type,company',
            'rs' => 'required_if:type,entity',
            'ks' => 'required_if:type,entity',
            'bik' => 'required_if:type,entity',
            'bank' => 'required_if:type,entity',
            'source' => 'required|in:site,avito',
        ];
    }
}