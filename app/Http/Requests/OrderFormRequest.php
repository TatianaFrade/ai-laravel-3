<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderFormRequest extends FormRequest
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
            'member_id' => 'required|integer|min:0',
            'status' => 'required|string|in:completed,canceled,pending',
            'date' => 'nullable|date',
            'total_items' => 'nullable|numeric',       
            'shipping_cost' => 'nullable|numeric',    
            'total' => 'nullable|numeric',             
            'nif' => ['nullable', 'digits:9'],        
            'delivery_address' => 'required|string',
            'cancel_reason' => 'required_if:status,cancelled|string|nullable',
        ];
    }

}
