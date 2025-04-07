<?php

// namespace App\Repository;

// use App\Models\Student;
// use App\Repository\Interfaces\StudentRepositoryInterface;
// use Carbon\Carbon;

// class StudentRepository implements StudentRepositoryInterface
// {
//     public function create(array $data)
//     {
//         return Student::create($data);
//     }

//     public function getStudents(array $filters)
//     {
//         $query = Student::query();

//         if (!empty($filters['user_id'])) {
//             $query->where('user_id', $filters['user_id']);
//         }

//         if (!empty($filters['name'])) {
//             $query->where('name', 'like', '%' . $filters['name'] . '%');
//         }

//         if (!empty($filters['email'])) {
//             $query->where('email', $filters['email']);
//         }

//         if (!empty($filters['age'])) {
//             $query->where('age', $filters['age']);
//         }

//         if (!empty($filters['course'])) {
//             $query->where('course', 'like', '%' . $filters['course'] . '%');
//         }

//         return $query->get();
//     }

//     public function update(int $id, array $data)
//     {
//         $student = Student::find($id);

//         if (!$student) {
//             return null;
//         }

//         $student->update($data);

//         return $student->fresh();
//     }
// }













// app/Repository/Eloquent/StudentRepository.php

namespace App\Repository;

use App\Models\Student;
use App\Repository\Interfaces\StudentRepositoryInterface;

class StudentRepository implements StudentRepositoryInterface
{
    protected Student $model;

    public function __construct(Student $model)
    {
        $this->model = $model;
    }

    public function checkEmailExists(string $email)
    {

        return $this->model->where('email', $email)->get();
    }

    public function create(array $data): array
    {
        $student = $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'age' => $data['age'],
            'course' => $data['course'],
            'created_date' => $data['created_date'],
            'updated_date' => $data['updated_date'],
        ]);

        return $student->toArray();
    }


    public function checkEmailExistsForOther(string $email, int $excludeId)
    {
        return $this->model
            ->where('email', $email)
            ->where('id', '!=', $excludeId)
            ->get();
    }

    public function findById(int $id): ?array
    {
        $student = $this->model->find($id);

        return $student ? $student->toArray() : null;
    }

    public function update(array $existingStudent, array $data): ?array
    {
        $student = $this->model->find($existingStudent['id']);

        if (!$student) {
            return null;
        }

        $student->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'age' => $data['age'],
            'course' => $data['course'],
            'updated_date' => $data['updated_date'],
        ]);

        return $student->toArray();
    }


    public function deleteById(int $id): bool
    {
        $student = $this->model->find($id);

        if ($student) {
            return $student->delete();
        }

        return false;
    }

    
    public function getStudent(array $filters): array
    {
        $query = $this->model->query();

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (!empty($filters['name'])) {
            $query->where('name', 'LIKE', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'LIKE', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['age'])) {
            $query->where('age', $filters['age']);
        }

        if (!empty($filters['course'])) {
            $query->where('course', 'LIKE', '%' . $filters['course'] . '%');
        }

        return $query->get()->toArray();
    }
}
