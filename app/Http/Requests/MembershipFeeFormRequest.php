<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\MembershipFee;

class MembershipFeeFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $membershipfee = $this->route('membership_fee') ?? MembershipFee::first();
        return auth()->user()?->can('update', $membershipfee);
    }

    public function rules(): array
    {
        return [
            'membership_fee' => 'required|numeric|min:0',
        ];
    }
}
