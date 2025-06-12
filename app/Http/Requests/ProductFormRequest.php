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

    /**
     * Custom validation function for stock upper limit
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure $fail
     * @return void
     */
    public function validateStockUpperLimit($attribute, $value, $fail)
    {
        // Check if we're in employee mode (updating only stock)
        if ($this->user()?->type === 'employee') {
            $product = $this->route('product');
            if ($product && $value > $product->stock_upper_limit) {
                $fail('The stock cannot exceed the stock upper limit (' . $product->stock_upper_limit . ').');
            }
        } else {
            // We're in board mode (creating/updating all fields)
            $upperLimit = $this->input('stock_upper_limit');
            if ($value > $upperLimit) {
                $fail('The stock cannot exceed the stock upper limit (' . $upperLimit . ').');
            }
        }
    }

    public function rules(): array
    {
        // Só precisa validar stock para employee
        if ($this->user()?->type === 'employee') {
            return [
                'stock' => [
                    'required',
                    'integer',
                    'min:0',
                    [$this, 'validateStockUpperLimit'],
                ],
            ];
        }

        // Board pode validar tudo
        return [
            'category_id'        => 'required|integer|exists:categories,id',
            'name'               => 'required|string|max:255',
            'price'              => 'required|numeric|min:0',
            'stock'              => [
                'required',
                'integer',
                'min:0',
                [$this, 'validateStockUpperLimit'],
            ],
            'description'        => 'required|string',
            'stock_lower_limit'  => 'required|integer|min:0',
            'stock_upper_limit'  => 'required|integer|min:0',
            'discount_min_qty'   => 'nullable|integer|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'photo'              => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
        ];
    }

}
