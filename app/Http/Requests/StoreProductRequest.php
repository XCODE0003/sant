<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug'],
            'article_id' => ['required', 'string', 'max:255', 'unique:products,article_id'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'integer', 'min:0', 'max:90'],
            'description' => ['nullable', 'string'],
            'characteristics' => ['nullable', 'array'],
            'characteristics.*' => ['sometimes'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}

