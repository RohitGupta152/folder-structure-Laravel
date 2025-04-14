<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\BO\AuthBO;
use App\Modules\Auth\Loggers\AuthLogger;
use App\Modules\Auth\Validators\AuthValidator;
use App\Repository\Interfaces\AuthRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

use App\Repository\Interfaces\UserRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

class AuthService
{
    protected $authRepositoryInterface;
    protected $authValidator;
    protected $authLogger;
    protected $userRepositoryInterface;


    public function __construct(
        AuthRepositoryInterface $authRepositoryInterface,
        AuthValidator $authValidator,
        AuthLogger $authLogger,
        UserRepositoryInterface $userRepositoryInterface
    ) {
        $this->authRepositoryInterface = $authRepositoryInterface;
        $this->authValidator = $authValidator;
        $this->authLogger = $authLogger;
        $this->userRepositoryInterface = $userRepositoryInterface;
    }

    public function registerUser(AuthBO $authBO): array
    {
        try {
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $authBO->setCreatedDate($now);
            $authBO->setUpdatedDate($now);

            $checkEmailExists = $this->authRepositoryInterface->checkEmailExists($authBO->getEmail());
            $isEmailTaken = $checkEmailExists->isNotEmpty();

            $this->authValidator->validateForRegister($authBO, $isEmailTaken);

            $createdUser = $this->authLogger->registerUser($authBO->toArray());

            if (!$createdUser) {
                return [
                    'status' => 'error',
                    'message' => 'Registration failed',
                    'status_code' => 400
                ];
            }

            return [
                'status' => 'success',
                'message' => 'User registered successfully. A welcome email has been sent!',
                'status_code' => 201
            ];
        } catch (\Exception $e) {
            Log::error('RegisterUser Exception: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Failed to register user',
                'errors' => [$e->getMessage()],
                'status_code' => 422
            ];
        }
    }

    public function adminRegister(AuthBO $authBO): array
    {
        try {
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $authBO->setCreatedDate($now);
            $authBO->setUpdatedDate($now);

            $checkEmailExists = $this->authRepositoryInterface->checkEmailExists($authBO->getEmail());
            $isEmailTaken = $checkEmailExists->isNotEmpty();

            $this->authValidator->validateForAdminRegister($authBO, $isEmailTaken);

            $createdAdmin = $this->authLogger->registerUser($authBO->toArray());

            if (!$createdAdmin) {
                return [
                    'status' => 'error',
                    'message' => 'Admin registration failed',
                    'status_code' => 400
                ];
            }

            return [
                'status' => 'success',
                'message' => 'Admin registered successfully',
                'status_code' => 201
            ];
        } catch (\Exception $e) {
            Log::error('AdminRegister Exception: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Failed to register admin',
                'errors' => [$e->getMessage()],
                'status_code' => 422
            ];
        }
    }

    public function loginUser(AuthBO $authBO): array
    {
        try {
            $this->authValidator->validateForLogin($authBO);

            $user = $this->authRepositoryInterface->getUserByEmail($authBO->getEmail());
            // dd($user);

            if (!$user || !password_verify($authBO->getPassword(), $user->password)) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid email or password',
                    'status_code' => 401
                ];
            }

            // Check if requested user_type matches the DB one
            if ($authBO->getUserType() !== null && $authBO->getUserType() != $user->user_type) {
                return [
                    'status' => 'error',
                    'message' => 'User type mismatch',
                    'status_code' => 403
                ];
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            $rememberToken = $token;
            $user->remember_token = $rememberToken;
            $user->save();

            return [
                'status' => 'success',
                'message' => 'Login successful',
                'status_code' => 200,
                'data' => [
                    'email' => $user->email,
                    'name' => $user->name,
                    'user_type' => $user->user_type,
                    'token' => $token,
                ]
            ];
        } catch (\Exception $e) {
            Log::error('LoginUser Exception: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Failed to login',
                'errors' => [$e->getMessage()],
                'status_code' => 422
            ];
        }
    }

    public function updateUser(AuthBO $authBO): array
    {
        try {
            $authBO->setUpdatedDate(Carbon::now()->format('Y-m-d H:i:s'));

            $existingUser = $this->authRepositoryInterface->getUserById($authBO->getId());
            // dd($existingUser);

            if (!$existingUser) {
                return [
                    'status' => 'error',
                    'message' => 'User not found',
                    'status_code' => 404
                ];
            }

            // Check if email is being updated and already taken by another user
            $checkEmailExistsForOtherUser = $this->authRepositoryInterface->checkEmailExistsForOtherUser(
                $authBO->getEmail(),
                $authBO->getId()
            );
            $isEmailTaken = $checkEmailExistsForOtherUser->isNotEmpty();
            // dd($isEmailTaken);

            // DB-level validation
            $this->authValidator->validateForUpdate($authBO, $isEmailTaken);

            // Perform update
            $updated = $this->authLogger->updateUser($authBO->getId(), $authBO->toArray());

            if (!$updated) {
                return [
                    'status' => 'error',
                    'message' => 'Update failed',
                    'status_code' => 400
                ];
            }

            return [
                'status' => 'success',
                'message' => 'User updated successfully',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            Log::error('UpdateUser Exception: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Failed to update user',
                'errors' => [$e->getMessage()],
                'status_code' => 422
            ];
        }
    }

    public function logoutUser(AuthBO $authBO): array
    {
        try {
            $user = $this->authRepositoryInterface->getUserById($authBO->getId());

            if (!$user) {
                return [
                    'status' => 'error',
                    'message' => 'User not found',
                    'status_code' => 404
                ];
            }

            $user->tokens()->delete();

            return [
                'status' => 'success',
                'message' => 'Logout successful',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            Log::error('LogoutUser Service Exception: ' . $e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Failed to logout',
                'errors' => [$e->getMessage()],
                'status_code' => 500
            ];
        }
    }









    public function createUser(array $data)
    {
        if (Gate::denies('create', User::class)) {
            throw new AuthorizationException('Access denied: Only Admins can create users.');
        }
        return $this->userRepositoryInterface->create($data);
    }

    public function getUserList()
    {
        if (Gate::denies('viewAny', User::class)) {
            throw new AuthorizationException(message: 'Access denied: Only Admins and Sub-Admins can view users.');
        }

        // Use Policies instead of Gate
        // $this->authorize('viewAny', User::class);

        $users = $this->userRepositoryInterface->getAll();

        return $this->formatUserList($users);
    }

    public function formatUserList($users)
    {
        $formattedUsers = [];

        foreach ($users as $user) {
            $formattedUsers[] = [
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'created_at' => date('d M y  h:i A', strtotime($user['created_at'])),
                'updated_at' => date('d M y  h:i A', strtotime($user['updated_at'])),
            ];
        }

        return $formattedUsers;
    }


    public function getUserById($userId)
    {
        $user = $this->userRepositoryInterface->findById($userId);

        if (Gate::denies('view', $user)) {
            throw new AuthorizationException('Access denied: You cannot view this user.');
        }

        $users = $this->formatUser($user);

        return $users;
    }

    public function formatUser($user)
    {
        return [
            'name' => $user->name,
            'email' => $user->email,
            'user_type' => $user->user_type,
            'created_at' => date('d M y  h:i A', strtotime($user['created_at'])),
            'updated_at' => date('d M y  h:i A', strtotime($user['updated_at'])),
        ];
    }

    public function updateUserById($userId, array $data)
    {
        $user = $this->userRepositoryInterface->findById($userId);

        if (Gate::denies('update', $user)) {
            throw new AuthorizationException('Access denied: You cannot update this user.');
        }
        return $this->userRepositoryInterface->update($userId, $data);
    }

    public function updateUserByIdAndRole($userId, array $data)
    {
        $user = $this->userRepositoryInterface->findById($userId);
        if (Gate::denies('updateRole', $user)) {
            throw new AuthorizationException('Access denied: You cannot update this user\'s role.');
        }
        return $this->userRepositoryInterface->update($userId, $data);
    }

    public function deleteUserById($userId, $data)
    {
        $user = $this->userRepositoryInterface->findById($userId);
        // dd($user);
        // dd($user->toArray());

        if (Gate::denies('delete', $user)) {
            // dd(Gate::denies('delete', $user));
            throw new AuthorizationException('Access denied: Only administrators can delete users.');
        }
        $this->userRepositoryInterface->delete($userId, $data);
    }
}
