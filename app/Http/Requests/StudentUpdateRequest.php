<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'     => 'required|numeric',
            'name'   => 'required|string|max:255',
            'email'  => 'required|email',
            'age'    => 'required|integer|min:1|max:60',
            'course' => 'required|string|max:100',
        ];
    }

    public function messages(){
        return[
            'id.required'     => 'Student Id is required ',
            'name.required'   => 'Student name is required.',
            'email.required'  => 'Email is required.',
            'email.email'     => 'Please provide a valid email address.',
            // 'email.unique'    => 'This email is already registered.',
            'age.required'    => 'Age is required.',
            'age.integer'     => 'Age must be a valid number.',
            'age.min'         => 'Age must be at least 1.',
            'age.max'         => 'Age must not exceed 150.',
            'course.required' => 'Course is required.',
        ];
    }
}
