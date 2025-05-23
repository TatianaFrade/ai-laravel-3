<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sem policies, todos podem submeter
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'image'              => 'nullable|string|max:255', 
          
        ];
    }
}
