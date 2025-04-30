<?php


namespace App\Repository;

use App\Repository\Interfaces\AuthRepositoryInterface;
use App\Events\UserRegistered;
use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    public function create(array $data): array
    {
        $user = User::create($data);
        event(new UserRegistered($user));
        return $user->toArray();
    }

    public function checkEmailExists(string $email)
    {
        return User::where('email', $email)->get();
    }

    public function getUserByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function update(int $id, array $data): bool
    {
        return User::where('id', $id)->update($data);
    }

    public function getUserById(int $id)
    {
        return User::find($id);
    }

    public function checkEmailExistsForOtherUser(string $email, int $excludeId)
    {
        return User::where('email', $email)
            ->where('id', '!=', $excludeId)
            ->get();
    }
}
