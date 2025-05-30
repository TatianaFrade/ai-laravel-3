<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class ProductFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        if ($this->isMethod('post')) {
            return $this->user()?->can('create', Product::class);
        }

        if ($product) {
            return $this->user()?->can('update', $product);
        }

        return false;
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

        if ($this->hasFile('photo')) {
            $rules['photo'] = ['image', 'mimes:jpeg,png,jpg,gif,svg'];
        }

        return $rules;
    }
}
