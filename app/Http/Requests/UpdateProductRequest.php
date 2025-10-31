<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id ?? $this->route('product');

        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', "unique:products,slug,{$productId}"],
            'article_id' => ['sometimes', 'string', 'max:255', "unique:products,article_id,{$productId}"],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'discount' => ['sometimes', 'integer', 'min:0', 'max:90'],
            'description' => ['nullable', 'string'],
            'characteristics' => ['nullable', 'array'],
            'characteristics.*' => ['sometimes'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}

