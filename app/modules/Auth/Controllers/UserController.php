<?php


namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateUserRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Requests\Auth\UpdateUserRoleRequest;
use App\Http\Requests\UserRequest;
use App\Modules\Auth\Services\AuthService;
use App\Services\GetUserService;
use App\Services\Users\Add\CreateUserService;
use App\Services\Users\Edit\DeleteUserByIdService;
use App\Services\Users\Edit\UpdateUserByIdAndRoleService;
use App\Services\Users\Edit\UpdateUserByIdService;
use App\Services\Users\Get\GetUserByIdService;
use App\Services\Users\Get\GetUserListService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

// class UserController extends Controller
// {
//     use AuthorizesRequests;
//     protected $userService;

//     public function __construct(UserService $userService)
//     {
//         $this->userService = $userService;
//     }

//     public function getUserList(Request $request): JsonResponse
//     {
//         $users = $this->userService->getUserList();
//         // dd($users);
//         return response()->json([
//             'status' => 'success',
//             // 'message' => 'User list retrieved successfully.',
//             'data' => $users
//         ]);
//     }

//     public function getUserById(Request $request): JsonResponse
//     {
//         $user = $this->userService->getUserById($request->user_id);
//         return response()->json([
//             'status' => 'success',
//             // 'message' => 'User retrieved successfully.',
//             'data' => $user
//         ]);
//     }

//     public function createUser(CreateUserRequest $request): JsonResponse
//     {
//         $data = [
//             'name' => $request['name'],
//             'email' => $request['email'],
//             'password' => $request['password'],
//             'user_type' => $request['user_type'],
//         ];

//         // dd($data);

//         $user = $this->userService->createUser($data);
//         return response()->json([
//             'status' => 'success',
//             'message' => 'User created successfully.',
//             'data' => $user
//         ], 201);
//     }

//     public function updateUserById(UpdateUserRequest $request): JsonResponse
//     {
//         $userId = $request->user_id;
//         $data = [
//             'user_id' => $request['user_id'],
//             'name' => $request['name'],
//             'email' => $request['email'],
//             'password' => $request['password'],
//             // 'user_type' => $request['user_type'],
//         ];

//         // dd($data, $userId);

//         $user = $this->userService->updateUserById($userId, $data);
//         return response()->json([
//             'status' => 'success',
//             'message' => 'User updated successfully.',
//             // 'data' => $user
//         ]);
//     }

//     public function updateUserByIdAndRole(UpdateUserRoleRequest $request): JsonResponse
//     {
//         $userId = $request->user_id;
//         $data = [
//             'user_id' => $request['user_id'],
//             'name' => $request['name'],
//             'email' => $request['email'],
//             'password' => $request['password'],
//             'user_type' => $request['user_type'],
//         ];
//         // dd($data, $userId);

//         $user = $this->userService->updateUserByIdAndRole($userId, $data);
//         return response()->json([
//             'status' => 'success',
//             'message' => 'User and role updated successfully.',
//             // 'data' => $user
//         ]);
//     }

//     public function deleteUserById(Request $request): JsonResponse
//     {
//         $userId = $request->user_id;
//         $data = [
//             'user_id' => $request['user_id'],
//         ];
//         // dd($userId, $data);

//         $this->userService->deleteUserById($userId, $data);
//         return response()->json([
//             'status' => 'success',
//             'message' => 'User deleted successfully.'
//         ]);
//     }
// }


class UserController extends Controller
{
    use AuthorizesRequests;

    public function getUserList(Request $request): JsonResponse
    {
        $GetUserListService = app(AuthService::class);

        $users = $GetUserListService->getUserList();
        // dd($users);
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function getUserById(Request $request): JsonResponse
    {
        $GetUserByIdService = app(AuthService::class);

        $userId = $request->user_id;

        $user = $GetUserByIdService->getUserById($userId);
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function createUser(Request $request): JsonResponse
    {
        $CreateUserService = app(AuthService::class);

        $data = [
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $request['password'],
            'user_type' => $request['user_type'],
        ];

        // dd($data);

        $user = $CreateUserService->createUser($data);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully.',
            'data' => $user
        ], 201);
    }

    public function updateUserById(Request $request): JsonResponse
    {
        $UpdateUserByIdService = app(AuthService::class);

        $userId = $request->user_id;
        $data = [
            'user_id' => $request['user_id'],
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $request['password'],
            // 'user_type' => $request['user_type'],
        ];

        // dd($data, $userId);

        $user = $UpdateUserByIdService->updateUserById($userId, $data);
        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully.',
            // 'data' => $user
        ]);
    }

    public function updateUserByIdAndRole(Request $request): JsonResponse
    {
        $UpdateUserByIdAndRoleService = app(AuthService::class);

        $userId = $request->user_id;
        $data = [
            'user_id' => $request['user_id'],
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => $request['password'],
            'user_type' => $request['user_type'],
        ];
        // dd($data, $userId);

        $user = $UpdateUserByIdAndRoleService->updateUserByIdAndRole($userId, $data);
        return response()->json([
            'status' => 'success',
            'message' => 'User and role updated successfully.',
            // 'data' => $user
        ]);
    }

    public function deleteUserById(Request $request): JsonResponse
    {
        $DeleteUserByIdService = app(AuthService::class);

        $userId = $request->user_id;
        $data = [
            'user_id' => $request['user_id'],
        ];
        // dd($userId, $data);

        $DeleteUserByIdService->deleteUserById($userId, $data);
        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully.'
        ]);
    }
}
