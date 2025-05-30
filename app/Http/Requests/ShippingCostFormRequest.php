<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ShippingCost;

class ShippingCostFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();

        $shippingCost = $this->route('shipping_cost');

        if ($shippingCost) {
            return $user && $user->can('update', $shippingCost);
        }

        return $user && $user->can('create', ShippingCost::class);
    }


    public function rules(): array
    {
        return [
            'min_value_threshold' => 'required|numeric|min:0',
            'max_value_threshold' => 'nullable|numeric|gt:0',
            'shipping_cost'       => 'required|numeric|min:0',
        ];
    }
}
