<?php

namespace App\Modules\Auth\Loggers;

use App\Repository\AuthRepository;
use App\Repository\Interfaces\AuthRepositoryInterface;

class AuthLogger
{
    protected $authRepositoryInterface;

    public function __construct(
        AuthRepositoryInterface $authRepositoryInterface,
    ) {
        $this->authRepositoryInterface = $authRepositoryInterface;
    }



    public function registerUser(array $userData)
    {
        $userData = $this->authRepositoryInterface->create($userData);
        return $userData;
    }

    public function updateUser(int $userId, array $userData)
    {
        $updatedData = $this->authRepositoryInterface->update($userId, $userData);
        return $updatedData;
    }
}
