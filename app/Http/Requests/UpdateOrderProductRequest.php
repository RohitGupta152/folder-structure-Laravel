<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderProductRequest extends FormRequest
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
            'order_no' => 'required|string',

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
