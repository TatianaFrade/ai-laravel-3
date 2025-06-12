<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

class ProductFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        if ($product) {
            return $this->user()?->can('update', $product) || $this->user()?->can('updateStock', $product);
        }

        if ($this->isMethod('post')) {
            return $this->user()?->can('create', Product::class);
        }

        return false;
    }

    public function rules(): array
    {
        // Só precisa validar stock para employee
        if ($this->user()?->type === 'employee') {
            return [
                'stock' => 'required|integer|min:0',
            ];
        }

        // Board pode validar tudo
        return [
            'category_id'        => 'required|integer|exists:categories,id',
            'name'               => 'required|string|max:255',
            'price'              => 'required|numeric|min:0',
            'stock'              => 'required|integer|min:0',
            'description'        => 'required|string',
            'stock_lower_limit'  => 'required|integer|min:0',
            'stock_upper_limit'  => 'required|integer|min:0',
            'discount_min_qty'   => 'nullable|integer|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'photo'              => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ];
    }

}
