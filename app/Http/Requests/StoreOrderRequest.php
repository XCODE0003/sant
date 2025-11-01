<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:32'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'delivery_method' => ['required', 'in:courier,pickup'],
            'payment_method' => ['required', 'in:cash,card'],
            'address' => ['nullable', 'array'],
            'address.city' => ['required', 'string', 'max:255'],
            'address.street' => ['required_if:delivery_method,courier', 'nullable', 'string', 'max:255'],
            'address.house' => ['required_if:delivery_method,courier', 'nullable', 'string', 'max:255'],
            'address.apartment' => ['nullable', 'string', 'max:255'],
            'address.entrance' => ['nullable', 'string', 'max:255'],
            'address.comment' => ['nullable', 'string', 'max:1000'],
            'address.is_private_house' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'integer', 'exists:products,id'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.slug' => ['nullable', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
            'items.*.final_price' => ['required', 'numeric', 'min:0'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'agreement' => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $address = $this->input('address', []);

        if (is_array($address)) {
            $address['is_private_house'] = filter_var($address['is_private_house'] ?? false, FILTER_VALIDATE_BOOL);

            if ($address['is_private_house']) {
                $address['apartment'] = null;
                $address['entrance'] = null;
            }

            $this->merge([
                'address' => $address,
            ]);
        }
    }
}

