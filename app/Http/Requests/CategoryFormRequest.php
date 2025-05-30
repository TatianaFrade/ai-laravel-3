<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class CategoryFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $category = $this->route('category');

        if ($this->isMethod('post')) {
            return $this->user()?->can('create', Category::class);
        }

        if ($category instanceof Category) {
            return $this->user()?->can('update', $category);
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
