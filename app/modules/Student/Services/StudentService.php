<?php

namespace App\Modules\Student\Services;

use App\Modules\Student\BO\StudentBO;
use App\Modules\Student\Helpers\StudentHelper;
use App\Modules\Student\Loggers\StudentLogger;
use App\Modules\Student\Validators\StudentValidator;
use App\Repository\Interfaces\StudentRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;


class StudentService
{
    protected StudentRepositoryInterface $studentRepositoryInterface;
    protected StudentValidator $studentValidator;
    protected StudentLogger $studentLogger;
    protected StudentHelper $studentHelper;

    public function __construct(
        StudentRepositoryInterface $studentRepositoryInterface,
        StudentValidator $studentValidator,
        StudentLogger $studentLogger,
        StudentHelper $studentHelper
    ) {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
        $this->studentValidator = $studentValidator;
        $this->studentLogger = $studentLogger;
        $this->studentHelper = $studentHelper;
    }


    public function createStudent(StudentBO $studentBO): array
    {
        try {
            // Set current date for created_date and updated_date
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $studentBO->setCreatedDate($now);
            $studentBO->setUpdatedDate($now);
            // dd($student);

            // Step 1: Check email existence via repository
            $checkEmailExists = $this->studentRepositoryInterface->checkEmailExists($studentBO->getEmail());
            $isEmailTaken = $checkEmailExists->isNotEmpty();

            // Step 2: Pass flag to validator (which throws exception if invalid)
            $this->studentValidator->validateForCreate($studentBO, $isEmailTaken);

            $createdStudent = $this->studentRepositoryInterface->create($studentBO->toArray());

            if (!$createdStudent) {
                return [
                    'status' => 'error',
                    'message' => 'Student creation failed',
                ];
            }

            $this->studentLogger->insertLog('Insert', $createdStudent);

            return [
                'status' => 'success',
                'message' => 'Student created successfully'
            ];
        } catch (Exception $e) {

            return [
                'status' => 'error',
                'message' => 'Failed to create student',
                'errors' => [$e->getMessage()]
            ];
        }
    }

    public function updateStudent(StudentBO $studentBO): array
    {
        try {
            // Set updated date
            $studentBO->setUpdatedDate(Carbon::now()->format('Y-m-d H:i:s'));

            // Step 1: Check if email already exists for another student
            $checkEmailExists = $this->studentRepositoryInterface->checkEmailExistsForOther(
                $studentBO->getEmail(),
                $studentBO->getId()
            );
            // dd($checkEmailExists);
            $isEmailTaken = $checkEmailExists->isNotEmpty();

            // Step 2: Validate update (throws exception if invalid)
            $this->studentValidator->validateForUpdate($studentBO, $isEmailTaken);

            // Step 3: Fetch existing student
            $existingStudent = $this->studentRepositoryInterface->findById($studentBO->getId());
            // dd($existingStudent);

            if (!$existingStudent) {
                return [
                    'status' => 'error',
                    'message' => 'Student not found',
                ];
            }

            // Step 4: Perform update
            $updatedStudent = $this->studentRepositoryInterface->update($existingStudent, $studentBO->toArray());

            if (!$updatedStudent) {
                return [
                    'status' => 'error',
                    'message' => 'Student update failed',
                ];
            }

            // Step 5: Log the update
            $this->studentLogger->updateLog('Update', $existingStudent, $studentBO->toArray());

            return [
                'status' => 'success',
                'message' => 'Student updated successfully'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to update student',
                'errors' => [$e->getMessage()]
            ];
        }
    }

    public function deleteStudent(StudentBO $studentBO): array
    {
        try {
            // Step 1: Fetch existing student
            $existingStudent = $this->studentRepositoryInterface->findById($studentBO->getId());

            if (!$existingStudent) {
                return [
                    'status' => 'error',
                    'message' => 'Student not found',
                ];
            }

            // Step 2: Delete from repository
            $deleted = $this->studentRepositoryInterface->deleteById($studentBO->getId());

            if (!$deleted) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to delete student',
                ];
            }

            // Step 3: Log the delete action
            $this->studentLogger->deleteLog('Delete', $existingStudent);

            return [
                'status' => 'success',
                'message' => 'Student deleted successfully'
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to delete student',
                'errors' => [$e->getMessage()]
            ];
        }
    }

    public function getStudent(StudentBO $studentBO): array
    {
        try {
            $filters = [
                'id' => $studentBO->getId(),
                'name' => $studentBO->getName(),
                'email' => $studentBO->getEmail(),
                'age' => $studentBO->getAge(),
                'course' => $studentBO->getCourse(),
            ];

            $result = $this->studentRepositoryInterface->getStudent($filters);

            if (empty($result)) {
                return [
                    'status' => 'error',
                    'data' => [],
                    'status_code' => 404
                ];
            }

            $resultFormat = $this->studentHelper->formatStudents($result);

            return [
                'status' => 'success',
                'data' => $resultFormat,
                'status_code' => 200
            ];
        } catch (Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'status' => 'error',
                'data' => [],
                'errors' => app()->environment('local') ? [$e->getMessage()] : null,
                'status_code' => 500
            ];
        }
    }

    public function exportStudent(StudentBO $studentBO): array
    {
        try {
            $filters = [
                'id'     => $studentBO->getId(),
                'name'   => $studentBO->getName(),
                'email'  => $studentBO->getEmail(),
                'age'    => $studentBO->getAge(),
                'course' => $studentBO->getCourse(),
            ];

            $students = $this->studentRepositoryInterface->getStudent($filters);

            if (empty($students)) {
                return [
                    'status' => 'error',
                    'message' => 'No student data found to export',
                    'status_code' => 404
                ];
            }

            $exportData = $this->studentHelper->formatStudentsForExport($students);

            $filePath = storage_path('app/public/filtered_students.csv');
            (new FastExcel(collect($exportData)))->export($filePath);

            return [
                'status' => 'success',
                'message' => 'Exported successfully',
                'file' => asset('storage/filtered_students.csv')
            ];
        } catch (Exception $e) {
            Log::error('Student export failed: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return [
                'status' => 'error',
                'message' => 'Export failed',
                'errors' => app()->environment('local') ? [$e->getMessage()] : [],
            ];
        }
    }
}
