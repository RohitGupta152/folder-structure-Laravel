<?php

namespace App\Modules\Student\Loggers;

use App\Models\Log;
use App\Repository\Interfaces\StudentRepositoryInterface;
use Carbon\Carbon;
use GuzzleHttp\Promise\Create;
use Illuminate\Support\Facades\Auth;

class StudentLogger
{

    protected $studentRepositoryInterface;

    public function __construct(StudentRepositoryInterface $studentRepositoryInterface)
    {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
    }


    public function insertLog(string $action, array $data): void
    {
        Log::create([
            'module' => 'Student',
            'action' => $action,
            'data' => json_encode([
                'student_id' => $data['id'],
                'student' => $data,
            ]),
            'performed_by' => Auth::id() ?? null,
            'performed_at' => Carbon::now(),
        ]);
    }

    public function updateLog(string $action, array $oldData, array $newData): void
    {
        Log::create([
            'module' => 'Student',
            'action' => 'Update',
            'data' => json_encode([
                'student_id' => $oldData['id'],
                'before' => $oldData,
                'after' => $newData
            ]),
            'performed_by' => Auth::id() ?? null,
            'performed_at' => Carbon::now(),
        ]);
    }

    public function deleteLog(string $action, array $data): void
    {
        Log::create([
            'module' => 'Student',
            'action' => $action,
            'data' => json_encode($data),
            'performed_by' => Auth::id() ?? null,
            'performed_at' => Carbon::now(),
        ]);
    }

    public function createStudent(array $studentData)
    {
        $studentData = $this->studentRepositoryInterface->create($studentData);
        return $studentData;
    }

    public function updateStudent(array $existingStudent, array $studentData)
    {
        $studentData = $this->studentRepositoryInterface->update($existingStudent, $studentData);
        return $studentData;
    }
}
