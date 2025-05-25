<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShippingCostFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sem policies, todos podem submeter
    }

    public function rules(): array
    {
        return [
            'min_value_threshold' => 'required|numeric|min:0',
            'max_value_threshold' => 'nullable|numeric|min:0',
            'shipping_cost'       => 'required|numeric|min:0',
      
      
        ];
    }
}
