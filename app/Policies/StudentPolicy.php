<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Student;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can create a student
     */
    public function create(User $user): bool
    {
        // Only users with type 3 can create students
        return $user->user_type === 3;
    }

    /**
     * Determine if the user can view students
     */
    public function viewAny(User $user): bool
    {
        // Only users with type 3 can view students
        return $user->user_type === 3;
    }

    /**
     * Determine if the user can view a specific student
     */
    public function view(User $user, Student $student): bool
    {
        // Users with type 3 can view any student
        // Or a user can view their own student record
        return $user->user_type === 3 ||
            $user->id === $student->user_id;
    }

    /**
     * Determine if the user can update a student
     */
    public function update(User $user, Student $student): bool
    {
        // Only users with type 3 can update students
        // Or a user can update their own student record
        return $user->user_type === 3 ||
            $user->id === $student->user_id;
    }

    /**
     * Determine if the user can delete a student
     */
    public function delete(User $user, Student $student): bool
    {
        // Only users with type 3 can delete students
        return $user->user_type === 3;
    }

    /**
     * Determine if the user can export students
     */
    public function export(User $user): bool
    {
        // Only users with type 3 can export students
        return $user->user_type === 3;
    }
}
