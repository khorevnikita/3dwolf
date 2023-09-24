<?php

namespace App\Http\Requests\Payment;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            "type" => "required|in:" . implode(',', Payment::TYPES),
            "user_id" => "sometimes|integer|exists:users,id|nullable",
            "order_id" => "sometimes|integer|exists:orders,id|nullable",
            "account_id" => "required|integer|exists:accounts,id",
            "paid_at" => "sometimes|date|nullable",
            "amount" => "required|numeric",
            "description" => "sometimes|max:5000",
        ];
    }
}
