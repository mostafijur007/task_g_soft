<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateKpiRequest extends FormRequest
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
            'entries' => 'required|array|min:1',
            'entries.*.id' => 'required|exists:k_p_i_entries,id',
            'entries.*.quantity' => 'nullable|integer|min:0',
            'entries.*.asp' => 'nullable|numeric|min:0',
            'entries.*.uom' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'entries.required' => 'You must provide at least one entry to update.',
            'entries.*.id.exists' => 'One or more KPI entry IDs are invalid.',
            'entries.*.month.date_format' => 'Month must be in YYYY-MM-DD format.',
        ];
    }
}
