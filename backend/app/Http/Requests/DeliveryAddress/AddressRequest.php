<?php

namespace App\Http\Requests\DeliveryAddress;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            "name" => "required|max:255",
            "description" => "sometimes|max:5000",
        ];
    }
}
