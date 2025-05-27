<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sem policies, todos podem submeter
    }

    public function rules(): array
    {
        $rules = [
            'category_id'        => 'required|integer|exists:categories,id',
            'name'               => 'required|string|max:255',
            'price'              => 'required|numeric|min:0',
            'stock'              => 'required|integer|min:0',
            'description'        => 'nullable|string',
            'stock_lower_limit'  => 'nullable|integer|min:0',
            'stock_upper_limit'  => 'nullable|integer|min:0',
            'discount_min_qty'   => 'nullable|integer|min:0',
            'discount'           => 'nullable|numeric|min:0',
        ];

        // Aplica regras de validação para 'photo' somente se houver arquivo enviado
        if ($this->hasFile('photo')) {
            $rules['photo'] = ['image', 'mimes:jpeg,png,jpg,gif,svg'];
        }

        return $rules;
    }
}
