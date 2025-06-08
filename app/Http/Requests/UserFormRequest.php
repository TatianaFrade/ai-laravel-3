<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserFormRequest extends FormRequest
{
    public function authorize(): bool
    {
       $user = $this->route('user');

        if ($this->isMethod('post')) {
            return $this->user()?->can('create', User::class);
        }

        if ($user) {
            return $this->user()?->can('update', $user);
        }

        return false;
    }

 
   public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'type' => 'required|in:member,board,employee',
           
            'gender' => 'required|in:F,M,O',
            'default_delivery_address' => 'nullable|string|max:255',
            'nif' => 'nullable|digits_between:8,14',
            'payment_details' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = 'required|string|max:255|min:8|confirmed';
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['password'] = 'nullable|string|max:255|min:8|confirmed';
        }

        return $rules;
    }

}
