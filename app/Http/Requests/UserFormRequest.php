<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'email' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'type' => 'required|in:member,board,employee',
            'gender' => 'required|in:F,M,O',
            'delivery_address' => 'required|string|min:1',
            'nif' => ['nullable', 'integer'],
            
            // 'profile_photo' => ['nullable', 'image', 'max:2048'],
    
        ];

        if (strtolower($this->getMethod()) == 'post') {
            // This will merge 2 arrays:
            // (adds the "abbreviation" rule to the $rules array)
          
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
           
        ];
    }
}
