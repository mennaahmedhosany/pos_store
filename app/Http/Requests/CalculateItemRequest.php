<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CalculateItemRequest extends FormRequest
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
            'cup_size_id'   => 'required|exists:cup_sizes,id',
            'water_type_id' => 'required|exists:water_types,id',
            'extra_ids'     => 'nullable|array',
            'extra_ids.*'   => 'exists:extras,id',
            'quantity'      => 'required|integer|min:1|max:99',
        ];
    }
}
