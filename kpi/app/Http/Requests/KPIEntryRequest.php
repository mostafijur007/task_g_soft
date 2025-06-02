<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KPIEntryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'month' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'], // YYYY-MM format
            'uom' => 'required|string|max:50',
            'quantity' => 'required|integer|min:0',
            'asp' => 'required|numeric|min:0',
            'total_value' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'month.regex' => 'The month must be in YYYY-MM format.',
        ];
    }
}
