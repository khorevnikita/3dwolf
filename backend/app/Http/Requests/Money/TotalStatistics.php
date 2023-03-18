<?php

namespace App\Http\Requests\Money;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class TotalStatistics extends FormRequest
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
            'year' => 'required|integer|min:2022|max:' . Carbon::now()->year,
        ];
    }
}
