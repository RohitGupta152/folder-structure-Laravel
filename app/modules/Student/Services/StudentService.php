<?php

namespace App\Modules\Student\Services;

use App\Http\Requests\Student\StudentCreateRequest;
use App\Http\Requests\Student\studentRequest;
use App\Modules\Student\BO\studentBo;
use App\Modules\Student\Helpers\StudentHelper;
use App\Modules\Student\Loggers\StudentLogger;
use App\Modules\Student\Validators\StudentValidator;
use App\Repository\StudentDAO\StudentDAO;
use App\Repository\Interfaces\StudentRepositoryInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;


class StudentService
{
    protected StudentRepositoryInterface $studentRepositoryInterface;
    protected StudentValidator $studentValidator;
    protected StudentLogger $studentLogger;
    protected StudentHelper $studentHelper;
    protected StudentBo $studentBo;
    protected StudentDAO $studentDAO;


    public function __construct(
        StudentRepositoryInterface $studentRepositoryInterface,
        StudentValidator $studentValidator,
        StudentLogger $studentLogger,
        StudentHelper $studentHelper,
        studentBo $studentBo,
        StudentDAO $studentDAO
    ) {
        $this->studentRepositoryInterface = $studentRepositoryInterface;
        $this->studentValidator = $studentValidator;
        $this->studentLogger = $studentLogger;
        $this->studentHelper = $studentHelper;
        $this->studentBo = $studentBo;
        $this->studentDAO = $studentDAO;
    }


    public function studentBo($studentRequest)
    {
        $this->studentBo->setId($studentRequest->input('id'));
        $this->studentBo->setName($studentRequest->input('name'));
        $this->studentBo->setEmail($studentRequest->input('email'));
        $this->studentBo->setAge($studentRequest->input('age'));
        $this->studentBo->setCourse($studentRequest->input('course'));
        $now = Carbon::now()->format('Y-m-d h:i:s');
        $this->studentBo->setCreatedDate($now);
        $this->studentBo->setUpdatedDate($now);
        return $this->studentBo;
    }

    public function studentDAO(StudentBO $StudentBo)
    {
        $this->studentDAO->setId($StudentBo->getId());
        $this->studentDAO->setName($StudentBo->getName());
        $this->studentDAO->setEmail($StudentBo->getEmail());
        $this->studentDAO->setAge($StudentBo->getAge());
        $this->studentDAO->setCourse($StudentBo->getCourse());
        $this->studentDAO->setCreatedDate($StudentBo->getCreatedDate());
        $this->studentDAO->setUpdatedDate($StudentBo->getUpdatedDate());
        // dd($studentDAO);
        return $this->studentDAO;
    }

    public function createStudent(StudentBo $studentBo): array
    {
        try {
            $studentData = $this->studentDAO($studentBo);

            $checkEmailExists = $this->studentRepositoryInterface->checkEmailExists($studentData->getEmail())->toArray();
            $this->studentValidator->validateForCreate($studentData->getEmail(), $checkEmailExists);

            $createdStudent = $this->studentLogger->createStudent($studentData->toArray());

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

    public function updateStudent(studentBo $studentBo): array
    {
        try {
            $studentData = $this->studentDAO($studentBo);

            $checkEmailExistsForOther = $this->studentRepositoryInterface->checkEmailExistsForOther(
                $studentData->getEmail(),
                $studentData->getId()
            )->toArray();

            $this->studentValidator->validateForUpdate($studentData->getEmail(), $checkEmailExistsForOther);

            $existingStudent = $this->studentRepositoryInterface->findById($studentData->getId());

            if (!$existingStudent) {
                return [
                    'status' => 'error',
                    'message' => 'Student not found',
                ];
            }

            $updatedStudent = $this->studentLogger->updateStudent($existingStudent, $studentData->toArray());

            if (!$updatedStudent) {
                return [
                    'status' => 'error',
                    'message' => 'Student update failed',
                ];
            }

            $this->studentLogger->updateLog('Update', $existingStudent, $studentData->toArray());

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

    public function deleteStudent(studentBo $studentBo): array
    {
        try {
            $studentData = $this->studentDAO($studentBo);

            $existingStudent = $this->studentRepositoryInterface->findById($studentData->getId());

            if (!$existingStudent) {
                return [
                    'status' => 'error',
                    'message' => 'Student not found',
                ];
            }

            $deleted = $this->studentRepositoryInterface->deleteById($studentData->getId());

            if (!$deleted) {
                return [
                    'status' => 'error',
                    'message' => 'Failed to delete student',
                ];
            }

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

    public function getStudent(studentBo $studentBo): array
    {
        try {
            $studentData = $this->studentDAO($studentBo);

            $filters = [
                'id' => $studentData->getId(),
                'name' => $studentData->getName(),
                'email' => $studentData->getEmail(),
                'age' => $studentData->getAge(),
                'course' => $studentData->getCourse(),
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

    public function exportStudent(studentBo $studentBo): array
    {
        try {
            $studentData = $this->studentDAO($studentBo);

            $filters = [
                'id'     => $studentData->getId(),
                'name'   => $studentData->getName(),
                'email'  => $studentData->getEmail(),
                'age'    => $studentData->getAge(),
                'course' => $studentData->getCourse(),
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

    public function handleImport($request)
    {
        $file = $request->file('file');
        $simpleFileName = 'student_import.csv';
        $filePath = $file->storeAs('imports', $simpleFileName, 'public');
        $fullPath = Storage::disk('public')->path($filePath);
        $lines = file($fullPath);

        $outputFile = fopen(Storage::disk('public')->path('imports/processed_' . basename($filePath)), 'w');

        $header = str_getcsv($lines[0]);
        $header[] = 'Status';
        $header[] = 'Message';
        fputcsv($outputFile, $header);

        array_shift($lines);
        $importCount = 0;

        foreach ($lines as $index => $line) {
            $data = str_getcsv($line);
            $status = 'Success';
            $message = '';

            if (count($data) >= 6) {
                $name = trim($data[0]);
                $email = trim($data[1]);
                $age = trim($data[2]);
                $course = trim($data[3]);
                $created_date = trim($data[4]);
                $updated_date = trim($data[5]);

                $checkEmailExists = $this->studentRepositoryInterface->checkEmailExists($email)->toArray();
                $validationError = $this->studentValidator->validateForImpCreate($email, $checkEmailExists, $age);

                if ($validationError) {

                    $status = 'Error';
                    $message = $validationError . ' - Data creation failed';
                } else {
                    $studentBo = new studentBo();
                    $studentBo->setName($name);
                    $studentBo->setEmail($email);
                    $studentBo->setAge($age);
                    $studentBo->setCourse($course);
                    $studentBo->setCreatedDate($this->studentHelper->parseDate($created_date));
                    $studentBo->setUpdatedDate($this->studentHelper->parseDate($updated_date));

                    $this->studentLogger->createStudent($studentBo->toArray());
                    $importCount++;
                }
            } else {

                $status = 'Error';
                $message = 'Insufficient data columns';
            }

            $data[] = $status;
            $data[] = $message;

            fputcsv($outputFile, $data);
        }

        fclose($outputFile);

        return [
            'status' => 'success',
            'message' => $importCount . ' records imported successfully',
            'file_path' => Storage::url($filePath)
        ];
    }
}
