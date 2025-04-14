<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): Response
    {
        return $this->isAdminOrSubAdmin($user)
            ? Response::allow()
            : Response::deny('You do not have permission to view user records.');
    }

    public function view(User $user, User $model): Response
    {
        return $user->id === $model->id || $this->isAdminOrSubAdmin($user)
            ? Response::allow()
            : Response::deny('You are not allowed to view this user.');
    }

    public function create(User $user): Response
    {
        return $this->isAdmin($user)
            ? Response::allow()
            : Response::deny('Only administrators can create new users.');
    }

    public function update(User $user, User $model): Response
    {
        return $user->id === $model->id
            ? Response::allow()
            : Response::deny('You do not have permission to update this user.');
    }

    public function updateRole(User $user): Response
    {
        return $this->isSubAdmin($user) || $this->isAdmin($user)
            ? Response::allow()
            : Response::deny('You do not have permission to update this user.');
    }

    public function delete(User $user, User $model)
    {
        return $user->user_type === 1;
    }

    public function restore(User $user, User $model): Response
    {
        return $this->isAdmin($user)
            ? Response::allow()
            : Response::deny('Only administrators can restore users.');
    }

    public function forceDelete(User $user, User $model): Response
    {
        return $this->isAdmin($user)
            ? Response::allow()
            : Response::deny('Only administrators can permanently delete users.');
    }

    public function isAdmin(User $user): bool
    {
        return $user->user_type === 1;
    }

    public function isSubAdmin(User $user): bool
    {
        return $user->user_type === 2;
    }

    public function isUser(User $user): bool
    {
        return $user->user_type === 3;
    }

    public function isAdminOrSubAdmin(User $user): bool
    {
        return in_array($user->user_type, [1, 2]);
    }
}
