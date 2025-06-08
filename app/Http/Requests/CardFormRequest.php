<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Card;

class CardFormRequest extends FormRequest
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
        return [
            'card_num' => 'nullable|digits:16|numeric|not_regex:/^0/',
            'cvc' => 'nullable|digits:3|numeric|not_regex:/^0/',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|regex:/^\d{9}$/', // 9 dígitos, sem espaços ou outros caracteres
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|in:Visa,PayPal,MB WAY',
        ];
    }
}
