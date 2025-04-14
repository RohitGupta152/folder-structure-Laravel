<?php
// app/Modules/Student/Controllers/StudentController.php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;

use App\Http\Requests\Student\StudentCreateRequest;
use App\Http\Requests\Student\StudentDeleteRequest;
use App\Http\Requests\Student\StudentGetRequest;
use App\Http\Requests\Student\StudentUpdateRequest;
use App\Models\Student;
use App\Modules\Student\BO\StudentBO;
// use App\Modules\Student\Requests\StudentCreateRequest;
use App\Modules\Student\Services\StudentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Request;
// use Illuminate\Http\Request;



class StudentController extends Controller
{
    use AuthorizesRequests;
    public function createStudent(StudentCreateRequest $request): JsonResponse
    {
        try {
            $studentBO = app(StudentBO::class);
            $studentService = app(StudentService::class);

            $studentBO->setName($request->input('name'));
            $studentBO->setEmail($request->input('email'));
            $studentBO->setAge($request->input('age'));
            $studentBO->setCourse($request->input('course'));

            $result = $studentService->createStudent($studentBO);

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Student created successfully'
                ], 201);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
                'errors' => $result['errors'] ?? null
            ], 422);
        } catch (Exception $e) {
            // Log the error for debugging
            Log::error('Error creating student: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return a generic error response to the client
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while processing your request',
                'errors' => app()->environment('local') ? [$e->getMessage()] : null
            ], 500);
        }
    }

    public function updateStudent(StudentUpdateRequest $request): JsonResponse
    {
        try {
            $studentBO = app(StudentBO::class);
            $studentService = app(StudentService::class);

            $studentBO->setId($request->input('id')); // Required for update
            $studentBO->setName($request->input('name'));
            $studentBO->setEmail($request->input('email'));
            $studentBO->setAge($request->input('age'));
            $studentBO->setCourse($request->input('course'));
            // dd($StudentBO);

            $result = $studentService->updateStudent($studentBO);

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Student updated successfully'
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
                'errors' => $result['errors'] ?? null
            ], 422);
        } catch (Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred while updating the student',
                'errors' => app()->environment('local') ? [$e->getMessage()] : null
            ], 500);
        }
    }

    public function deleteStudent(StudentDeleteRequest $request): JsonResponse
    {
        try {
            $studentBO = app(StudentBO::class);
            $studentService = app(StudentService::class);

            $studentBO->setId($request->input('id'));
            // dd($studentBO);

            $result = $studentService->deleteStudent($studentBO);

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => $result['message'],
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
                'errors' => $result['errors'] ?? null
            ], 404);
        } catch (Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred',
                'errors' => app()->environment('local') ? [$e->getMessage()] : null
            ], 500);
        }
    }

    public function getStudent(StudentGetRequest $request): JsonResponse
    {
        try {
            // $this->authorize('viewAny', Student::class);
            $studentBO = app(StudentBO::class);
            $studentService = app(StudentService::class);

            $studentBO->setId($request->input('id'));
            $studentBO->setName($request->input('name'));
            $studentBO->setEmail($request->input('email'));
            $studentBO->setAge($request->input('age'));
            $studentBO->setCourse($request->input('course'));

            $result = $studentService->getStudent($studentBO);

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'data' => $result['data']
                ], $result['status_code']);
            }

            return response()->json([
                'status' => 'error',
                'data' => [],
            ], $result['status_code']);
        } catch (\Exception $e) {
            Log::error('Error getting students: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'errors' => app()->environment('local') ? [$e->getMessage()] : null
            ], 500);
        }
        // catch (AuthorizationException $e) {
        //     // Handle authorization errors
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Unauthorized: ' . $e->getMessage()
        //     ], 403);

        // }

    }

    public function exportStudent(StudentGetRequest $request): JsonResponse
    {
        try {
            $studentBO = app(StudentBO::class);
            $studentService = app(StudentService::class);

            $studentBO->setId($request->input('id'));
            $studentBO->setName($request->input('name'));
            $studentBO->setEmail($request->input('email'));
            $studentBO->setAge($request->input('age'));
            $studentBO->setCourse($request->input('course'));

            $result = $studentService->exportStudent($studentBO);

            if ($result['status'] === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Student data exported successfully',
                    'file' => $result['file']
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Export failed'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Export student error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'status' => 'error',
                'errors' => app()->environment('local') ? [$e->getMessage()] : null
            ], 500);
        }
    }
}
