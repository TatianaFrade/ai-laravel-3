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
        return [
            'category_id'        => 'required|integer|exists:categories,id',
            'name'               => 'required|string|max:255',
            'price'              => 'required|numeric|min:0',
            'stock'              => 'required|integer|min:0',
            'description'        => 'nullable|string',
            'photo' => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'stock_lower_limit'  => 'nullable|integer|min:0',
            'stock_upper_limit'  => 'nullable|integer|min:0',
            'discount_min_qty' => 'nullable|integer|min:0',
            'discount' => 'nullable|numeric|min:0',
        ];
    }
}
