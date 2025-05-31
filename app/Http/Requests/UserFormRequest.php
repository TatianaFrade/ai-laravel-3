<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'type' => 'required|in:member,board,employee',
            'blocked' => [
                'sometimes',
                Rule::requiredIf(function () {
                    return $this->input('type') === 'member';
                }),
                'boolean',
            ],
            'gender' => 'required|in:F,M,O',
            'default_delivery_address' => 'nullable|string|max:255',
            'nif' => 'nullable|digits_between:8,14',
            'payment_details' => 'nullable|string|max:255',
            'photo' => 'nullable|string|max:255',
            
        ];

        if ($this->isMethod('post')) {
            // Criação: password obrigatória
            $rules['password'] = 'required|string|max:255|min:8|confirmed';
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            // Atualização: password opcional
            $rules['password'] = 'nullable|string|max:255|min:8|confirmed';
        }

        return $rules;
    }


    public function messages(): array
    {
        return [
           
        ];
    }
}
