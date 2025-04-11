<?php

// namespace App\Modules\Student\Validators;

// class StudentValidator
// {
//     public function validate($data)
//     {
//         return isset($data['name'], $data['email'], $data['age'], $data['course']) &&
//                is_numeric($data['age']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL);
//     }
// }








// namespace App\Modules\Student\Validators;

// use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\ValidationException;

// class StudentValidator
// {
//     public function validate(array $data): array
//     {
//         $validator = Validator::make($data, [
//             'name' => 'required|string|max:255',
//             'email' => 'required|email|unique:students,email',
//             'age' => 'required|integer|min:1|max:150',
//             'course' => 'required|string|max:100',
//         ]);

//         if ($validator->fails()) {
//             throw new ValidationException($validator);
//         }

//         return $validator->validated();
//     }
// }














// namespace App\Modules\Student\Validators;

// use App\Models\Student;
// use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\ValidationException;

// class StudentValidator
// {
//     // public function validateCreate(array $data): array
//     // {
//     //     $errors = [];

//     //     if (empty($data['name']) || !is_string($data['name'])) {
//     //         $errors['name'][] = 'Student name is required and must be a string.';
//     //     }

//     //     if (empty($data['email'])) {
//     //         $errors['email'][] = 'Email is required.';
//     //     } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
//     //         $errors['email'][] = 'Invalid email format.';
//     //     } elseif (Student::where('email', $data['email'])->exists()) {
//     //         $errors['email'][] = 'This email is already registered.';
//     //     }

//     //     if (!isset($data['age']) || !is_numeric($data['age'])) {
//     //         $errors['age'][] = 'Age must be a valid number.';
//     //     } elseif ($data['age'] < 1 || $data['age'] > 150) {
//     //         $errors['age'][] = 'Age must be between 1 and 150.';
//     //     }

//     //     if (empty($data['course']) || !is_string($data['course'])) {
//     //         $errors['course'][] = 'Course is required and must be a string.';
//     //     }

//     //     if (!empty($errors)) {
//     //         throw ValidationException::withMessages($errors);
//     //     }

//     //     return [
//     //         'name' => trim($data['name']),
//     //         'email' => strtolower(trim($data['email'])),
//     //         'age' => (int) $data['age'],
//     //         'course' => trim($data['course']),
//     //     ];
//     // }

//     // public function validateUpdate(array $data): array
//     // {
//     //     $errors = [];

//     //     if (empty($data['id']) || !is_numeric($data['id'])) {
//     //         $errors['name'][] = 'Student ID is required and must be a Number.';
//     //     }

//     //     if (empty($data['name']) || !is_string($data['name'])) {
//     //         $errors['name'][] = 'Student name is required and must be a string.';
//     //     }

//     //     if (empty($data['email'])) {
//     //         $errors['email'][] = 'Email is required.';
//     //     } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
//     //         $errors['email'][] = 'Invalid email format.';
//     //     } elseif (Student::where('email', $data['email'])->where('id', '!=', $data['id'])->exists()) {
//     //         $errors['email'][] = 'This email is already used by another student.';
//     //     }

//     //     if (!isset($data['age']) || !is_numeric($data['age'])) {
//     //         $errors['age'][] = 'Age must be a valid number.';
//     //     } elseif ($data['age'] < 1 || $data['age'] > 150) {
//     //         $errors['age'][] = 'Age must be between 1 and 150.';
//     //     }

//     //     if (empty($data['course']) || !is_string($data['course'])) {
//     //         $errors['course'][] = 'Course is required and must be a string.';
//     //     }

//     //     if (!empty($errors)) {
//     //         throw ValidationException::withMessages($errors);
//     //     }

//     //     return [
//     //         'name' => trim($data['name']),
//     //         'email' => strtolower(trim($data['email'])),
//     //         'age' => (int) $data['age'],
//     //         'course' => trim($data['course']),
//     //     ];
//     // }


//     public function validatorCreate(array $data): array
//     {
//         $errors = [];

//         // Only check if email is already used
//         $existingStudent = Student::where('email', $data['email'] ?? '')->first();
//         // dd($existingStudent->toArray());
//         // dd($existingStudent);

//         if ($existingStudent) {
//             $errors['email'][] = 'This email is already registered.';
//         }

//         if (!empty($errors)) {
//             throw ValidationException::withMessages($errors);
//         }

//         // Return trimmed/normalized values if needed
//         return [
//             'name'   => trim($data['name']),
//             'email'  => strtolower(trim($data['email'])),
//             'age'    => (int) $data['age'],
//             'course' => trim($data['course']),
//         ];
//     }

//     public function validatorUpdate(array $data): array
//     {
//         $errors = [];

//         // Make sure the email is not used by other students (excluding the current one)
//         $existingStudent = Student::where('email', $data['email'] ?? '')
//             ->where('id', '!=', $data['id'] ?? 0)
//             ->first();

//         if ($existingStudent) {
//             $errors['email'][] = 'This email is already used by another student.';
//         }

//         if (!empty($errors)) {
//             throw ValidationException::withMessages($errors);
//         }

//         return [
//             'id'     => (int) $data['id'],
//             'name'   => trim($data['name']),
//             'email'  => strtolower(trim($data['email'])),
//             'age'    => (int) $data['age'],
//             'course' => trim($data['course']),
//         ];
//     }
// }















namespace App\Modules\Student\Validators;

use App\Modules\Student\BO\StudentBO;
use App\Repository\Interfaces\StudentRepositoryInterface;

class StudentValidator
{
    public function validateForCreate(StudentBO $student, bool $isEmailTaken): void
    {
        if ($student->getEmail() && $isEmailTaken) {
            throw new \Exception('Email already exists in the database');
        }
    }

    public function validateForUpdate(StudentBO $student, bool $isEmailTaken): void
    {
        if ($student->getEmail() && $isEmailTaken) {
            throw new \Exception('Email already exists for another student');
        }
    }


    
    
    
    
    
    // public function validateForCreate(StudentBO $student): array
    // {
    //     // dd($student);
    //     $errors = [];

    //     $checkEmailExists = $this->studentRepository->checkEmailExists($student->getEmail());
    //     $studentId = $student->getEmail();

    //     // dd($checkEmailExists->toArray());
    //     // dd($checkEmailExists->isNotEmpty());

    //     // Check if email already exists in DB (additional DB-level check)
    //     if ($studentId && $checkEmailExists->isNotEmpty()) {
    //         $errors[] = 'Email already exists in the database';
    //     }

    //     // dd($errors);
    //     // dd(empty($errors));

    //     // Additional DB-level validations can be added here

    //     if (!empty($errors)) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'Validation failed',
    //             'errors' => $errors
    //         ];
    //     }

    //     return [
    //         'status' => 'success',
    //         'message' => 'Validation passed'
    //     ];
    // }

    // public function validateForUpdate(StudentBO $student): array
    // {
    //     $errors = [];

    //     if ($student->getEmail()) {
    //         $exists = $this->studentRepository->checkEmailExistsForOther(
    //             $student->getEmail(),
    //             $student->getId()
    //         );
    //         // dd($exists);

    //         if ($exists->isNotEmpty()) {
    //             $errors[] = 'Email already exists for another student';
    //         }
    //     }

    //     if (!empty($errors)) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'Validation failed',
    //             'errors' => $errors
    //         ];
    //     }

    //     return [
    //         'status' => 'success',
    //         'message' => 'Validation passed'
    //     ];
    // }

    // public function validateForDelete(StudentBO $student): array
    // {
    //     $errors = [];

    //     $existingStudent = $this->studentRepository->findById($student->getId());
    //     // dd($existingStudent);

    //     if (!$existingStudent) {
    //         $errors[] = 'Student not found.';
    //     }

    //     if (!empty($errors)) {
    //         return [
    //             'status' => 'error',
    //             'message' => 'Validation failed',
    //             'errors' => $errors
    //         ];
    //     }

    //     return [
    //         'status' => 'success',
    //         'message' => 'Validation passed'
    //     ];
    // }
    
}
