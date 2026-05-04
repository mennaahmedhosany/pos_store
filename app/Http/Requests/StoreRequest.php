<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'notes'                    => 'nullable|string|max:500',
            'items'                    => 'required|array|min:1',
            'items.*.cup_size_id'      => 'required|exists:cup_sizes,id',
            'items.*.water_type_id'    => 'required|exists:water_types,id',
            'items.*.extra_ids'        => 'nullable|array',
            'items.*.extra_ids.*'      => 'exists:extras,id',
            'items.*.quantity'         => 'required|integer|min:1',
        ];
    }
}
