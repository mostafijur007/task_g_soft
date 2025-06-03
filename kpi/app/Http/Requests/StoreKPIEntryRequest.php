<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKPIEntryRequest extends FormRequest
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
            'entries' => 'required|array',
            'entries.*.customer_id' => 'required|exists:customers,id',
            'entries.*.product_id' => 'required|exists:products,id',
            'entries.*.supplier_id' => 'required|exists:suppliers,id',
            'entries.*.month' => 'required|date',
            'entries.*.uom' => 'required|string|max:10',
            'entries.*.quantity' => 'required|numeric|min:0',
            'entries.*.asp' => 'required|numeric|min:0',
            'entries.*.total_value' => 'required|numeric|min:0',
        ];
    }
}
