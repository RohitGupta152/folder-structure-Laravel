<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Modules\Auth\BO\AuthBO;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function registerUser(Request $request): JsonResponse
    {
        try {
            $authBO = app(AuthBO::class);
            $authService = app(AuthService::class);

            $authBO->setName($request->input('name'));
            $authBO->setEmail($request->input('email'));
            $authBO->setPassword($request->input('password'));
            // dd($authBO);

            $result = $authService->registerUser($authBO);

            return response()->json([
                'status' => $result['status'],
                'message' => $result['message'],
                'errors' => $result['errors'] ?? [],
            ], $result['status_code']);
        } catch (\Exception $e) {
            Log::error('Register User Exception: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'errors' => app()->environment('local') ? [$e->getMessage()] : [],
            ], 500);
        }
    }

    public function adminRegister(Request $request): JsonResponse
    {
        try {
            $authBO = app(AuthBO::class);
            $authService = app(AuthService::class);

            $authBO->setName($request->input('name'));
            $authBO->setEmail($request->input('email'));
            $authBO->setPassword($request->input('password'));
            $authBO->setUserType($request->input('user_type'));
            // dd($authBO);

            $result = $authService->adminRegister($authBO);

            return response()->json([
                'status' => $result['status'],
                'message' => $result['message'],
                'errors' => $result['errors'] ?? [],
            ], $result['status_code']);
        } catch (\Exception $e) {
            Log::error('Admin Register Exception: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'errors' => app()->environment('local') ? [$e->getMessage()] : [],
            ], 500);
        }
    }

    public function loginUser(Request $request): JsonResponse
    {
        try {
            $authBO = app(AuthBO::class);
            $authService = app(AuthService::class);

            $authBO->setEmail($request->input('email'));
            $authBO->setPassword($request->input('password'));
            $authBO->setUserType($request->input('user_type'));
            // dd($authBO);

            $result = $authService->loginUser($authBO);

            return response()->json([
                'status' => $result['status'],
                'message' => $result['message'],
                'errors' => $result['errors'] ?? [],
                'data' => $result['data'] ?? [],
            ], $result['status_code']);
        } catch (\Exception $e) {
            Log::error('Login Exception: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'errors' => app()->environment('local') ? [$e->getMessage()] : [],
            ], 500);
        }
    }

    public function getUser(Request $request): JsonResponse
    {
        try {
            $user = $request->user(); // authenticated via Sanctum

            if (!$user || !in_array($user->user_type, [1, 2, 3])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized or invalid user type',
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User retrieved successfully',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'wallet_amount' => $user->wallet_amount,
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('GetUser Exception: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve user',
                'errors' => [$e->getMessage()],
            ], 500);
        }
    }

    public function updateUser(Request $request): JsonResponse
    {
        try {
            $authBO = app(AuthBO::class);
            $authService = app(AuthService::class);

            $authBO->setId(Auth::id());
            $authBO->setName($request->input('name'));
            $authBO->setEmail($request->input('email'));
            $authBO->setPassword($request->input('password'));
            $authBO->setUserType(Auth::user()->user_type ?? 3);
            // dd($authBO);

            $result = $authService->updateUser($authBO);

            return response()->json([
                'status' => $result['status'],
                'message' => $result['message'],
                'errors' => $result['errors'] ?? [],
            ], $result['status_code']);
        } catch (\Exception $e) {
            Log::error('UpdateUser Exception: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'errors' => app()->environment('local') ? [$e->getMessage()] : [],
            ], 500);
        }
    }

    public function logoutUser(): JsonResponse
    {
        try {
            $authBO = app(AuthBO::class);
            $authService = app(AuthService::class);

            $authBO->setId(Auth::id()); // Token-based user ID

            $result = $authService->logoutUser($authBO);

            return response()->json([
                'status' => $result['status'],
                'message' => $result['message'],
                'errors' => $result['errors'] ?? [],
            ], $result['status_code']);
        } catch (\Exception $e) {
            Log::error('LogoutUser Exception: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'errors' => app()->environment('local') ? [$e->getMessage()] : [],
            ], 500);
        }
    }
}
