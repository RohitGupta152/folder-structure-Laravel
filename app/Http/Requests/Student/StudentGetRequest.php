<?php

namespace App\Http\Requests\Student;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StudentGetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;

        // return Gate::allows('view-students');
        // return Gate::allows('viewAny', Student::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id'     => 'nullable|numeric',
            'name'   => 'nullable|string|max:255',
            'email'  => 'nullable|email',
            'age'    => 'nullable|integer|min:1|max:60',
            'course' => 'nullable|string|max:100',
        ];
    }

    // public function failedAuthorization()
    // {
    //     throw new \Illuminate\Auth\Access\AuthorizationException(
    //         'You are not authorized to view students'
    //     );
    // }
    
}
