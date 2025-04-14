<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminUserRequest;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use Illuminate\Http\JsonResponse;
use App\Modules\Auth\BO\AuthBO;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;

class AuthController extends Controller
{
    use AuthorizesRequests;

    public function registerUser(RegisterUserRequest $request): JsonResponse
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

    public function adminRegister(AdminUserRequest $request): JsonResponse
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

    public function loginUser(LoginUserRequest $request): JsonResponse
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

    public function updateUser(UpdateUserRequest $request): JsonResponse
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



    public function adminSubAdminDashboard(Request $request)
    {
        $user = $request->user();
        abort_if(!$user, 401, 'Unauthorized - You need to log in first.');

        try {
            $this->authorize('isAdminOrSubAdmin', $user);
            return response()->json(['message' => 'Access granted: Welcome to the Admin & Sub-Admin Dashboard.'], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'Access denied: Only Admins and Sub-Admins can access this dashboard.'], 403);
        }
    }

    /**
     * Admin Dashboard
     */
    public function adminDashboard(Request $request)
    {
        $user = $request->user();
        abort_if(!$user, 401, 'Unauthorized - You need to log in first.');

        try {
            $this->authorize('isAdmin', $user);
            return response()->json(['message' => 'Access granted: Welcome to the Admin Dashboard.'], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'Access denied: Only Admins can access this dashboard.'], 403);
        }
    }

    /**
     * Sub-Admin Dashboard
     */
    public function subAdminDashboard(Request $request)
    {
        $user = $request->user();
        abort_if(!$user, 401, 'Unauthorized - You need to log in first.');

        try {
            $this->authorize('isSubAdmin', $user);
            return response()->json(['message' => 'Access granted: Welcome to the Sub-Admin Dashboard.'], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'Access denied: Only Sub-Admins can access this dashboard.'], 403);
        }
    }

    /**
     * User Dashboard
     */
    public function userDashboard(Request $request)
    {
        $user = $request->user();
        abort_if(!$user, 401, 'Unauthorized - You need to log in first.');

        try {
            $this->authorize('isUser', $user);
            return response()->json(['message' => 'Access granted: Welcome to the User Dashboard.'], 200);
        } catch (AuthorizationException $e) {
            return response()->json(['error' => 'Access denied: Only regular users can access this dashboard.'], 403);
        }
    }
}
