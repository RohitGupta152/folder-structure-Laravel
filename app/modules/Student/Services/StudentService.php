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

            $checkEmailExists = $this->studentRepositoryInterface->checkEmailExists($studentBO->getEmail());
            $isEmailTaken = $checkEmailExists->isNotEmpty();

            $this->studentValidator->validateForCreate($studentBO, $isEmailTaken);

            $createdStudent = $this->studentLogger->createStudent($studentBO->toArray());

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
            $studentBO->setUpdatedDate(Carbon::now()->format('Y-m-d H:i:s'));

            $checkEmailExistsForOther = $this->studentRepositoryInterface->checkEmailExistsForOther(
                $studentBO->getEmail(),
                $studentBO->getId()
            );
            $isEmailTaken = $checkEmailExistsForOther->isNotEmpty();

            $this->studentValidator->validateForUpdate($studentBO, $isEmailTaken);

            $existingStudent = $this->studentRepositoryInterface->findById($studentBO->getId());

            if (!$existingStudent) {
                return [
                    'status' => 'error',
                    'message' => 'Student not found',
                ];
            }

            $updatedStudent = $this->studentLogger->updateStudent($existingStudent, $studentBO->toArray());

            if (!$updatedStudent) {
                return [
                    'status' => 'error',
                    'message' => 'Student update failed',
                ];
            }

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
            $existingStudent = $this->studentRepositoryInterface->findById($studentBO->getId());

            if (!$existingStudent) {
                return [
                    'status' => 'error',
                    'message' => 'Student not found',
                ];
            }

            $deleted = $this->studentRepositoryInterface->deleteById($studentBO->getId());

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
            // $filters = $studentBO->toArray();
            // dd($filters);

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

    public function handleImport($request)
    {
        try {
            $file = $request->file('file');
            $fileContents = file($file->getPathname());
            array_shift($fileContents);

            $importCount = 0;
            $errorCount = 0;
            $errors = [];

            foreach ($fileContents as $index => $line) {
                $data = explode(',', $line);

                if (count($data) >= 6) {
                    try {
                        $studentBo = new StudentBO();
                        $studentBo->setName(trim($data[0]));
                        $studentBo->setEmail(trim($data[1]));
                        $studentBo->setAge((int)trim($data[2]));
                        $studentBo->setCourse(trim($data[3]));
                        $studentBo->setCreatedDate($this->studentHelper->parseDate(trim($data[4])));
                        $studentBo->setUpdatedDate($this->studentHelper->parseDate(trim($data[5])));

                        $checkEmailExists = $this->studentRepositoryInterface->checkEmailExists($studentBo->getEmail());
                        $this->studentValidator->validateForImpCreate($studentBo, $checkEmailExists);

                        $this->studentLogger->createStudent($studentBo->toArray());

                        $importCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = ['row' => $index + 2, 'error' => $e->getMessage()];
                    }
                } else {
                    $errorCount++;
                    $errors[] = ['row' => $index + 2, 'error' => 'Insufficient columns'];
                }
            }

            return response()->json([
                'status' => 'success',
                'total_rows' => count($fileContents),
                'imported_count' => $importCount,
                'error_count' => $errorCount,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Import failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
