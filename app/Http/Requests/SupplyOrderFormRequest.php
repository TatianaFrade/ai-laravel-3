<?php

namespace App\Http\Requests;

use App\Models\SupplyOrder;
use Illuminate\Foundation\Http\FormRequest;

class SupplyOrderFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $supplyorder = $this->route('supplyorder');

        if ($this->isMethod('post')) {
            return $this->user()?->can('create', SupplyOrder::class);
        }

        if ($supplyorder) {
            return $this->user()?->can('update', $supplyorder);
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'product_id'            => 'required|numeric|min:0',
            'registered_by_user_id' => 'nullable|numeric|min:0',
            'status'                => 'required|in:completed,requested',
            'quantity'              => 'required|numeric|min:0',
        ];
    }
}
