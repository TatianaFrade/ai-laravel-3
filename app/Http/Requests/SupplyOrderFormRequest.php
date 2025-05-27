<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplyOrderFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sem policies, todos podem submeter
    }

    public function rules(): array
    {
        return [
            'product_id'            => 'required|numeric|min:0',
            'registered_by_user_id' => 'nullable|numeric|min:0',
            'status'                => 'required|in:completed,requested',
            'quantity'              => 'required|numeric|min:0',

      
      
        ];
    }
}
