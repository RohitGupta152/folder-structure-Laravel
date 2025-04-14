<?php

namespace App\Http\Requests\RateChart;

use Illuminate\Foundation\Http\FormRequest;

class CreateRateRequest extends FormRequest
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
            'user_id' => 'required|integer',
            'data' => 'required|array|min:1',
            'data.*.weight' => 'required|numeric|min:0.1',
            'data.*.rate_amount' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'data.*.weight.distinct' => 'Each weight value must be unique within the request.',
        ];
    }
}
