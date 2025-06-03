<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartConfirmationFormRequest extends FormRequest
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
    public function rules()
    {
        return [
            'nif' => 'required|numeric|digits:9',
            'default_delivery_address' => 'required|string|max:255',
        ];
    }
}
