<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'order_id' => 'required|string',
            'customer_name' => 'required|string',
            'email' => 'required|email',

            'contact_no' => 'required|string|max:15',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'pin_code' => 'required|string|max:10',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'country' => 'required|string|max:100',

            'weight' => 'required|numeric|min:0.1',
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'height' => 'required|numeric|min:0',

            'products' => 'required|array',
            'products.*.product_name' => 'required|string',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.quantity' => 'required|integer|min:1'
        ];
    }
}
