<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
        return [
            'date' => 'required|date',
            'customer_id' => 'required|integer|exists:customers,id',
            'phone' => 'required',
          #  'amount' => 'required|numeric',
            'deadline' => 'required|date',
            'status' => 'required|in:new,printing,shipping,completed',
            'payment_status' => 'required|in:not_paid,part_paid,full_paid'
        ];
    }
}
