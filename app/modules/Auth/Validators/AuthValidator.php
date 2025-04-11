<?php

namespace App\Modules\Auth\Validators;

use App\Modules\Auth\BO\AuthBO;

class AuthValidator
{
    public function validateForRegister(AuthBO $authBO, bool $isEmailTaken): void
    {
        // dd($authBO);
        if ($authBO->getEmail() && $isEmailTaken) {
            throw new \Exception('Email already exists in the database');
        }
    }

    public function validateForAdminRegister(AuthBO $authBO, bool $isEmailTaken): void
    {
        if ($authBO->getEmail() && $isEmailTaken) {
            throw new \Exception('Email already exists in the database');
        }

        $userType = $authBO->getUserType();
        if (!in_array($userType, [1, 2])) {
            throw new \Exception('Invalid user type for admin registration. Only user_type 1 or 2 is allowed.');
        }
    }

    public function validateForLogin(AuthBO $authBO): void
    {

        if (!$authBO->getEmail() || !$authBO->getPassword()) {
            throw new \Exception('Email and password are required');
        }

        $userType = $authBO->getUserType();
        if (!in_array($userType, [1, 2, 3])) {
            throw new \Exception('Invalid user type. Allowed: 1, 2, 3');
        }
    }

    public function validateForUpdate(AuthBO $authBO, bool $isEmailTaken): void
    {
        if (!$authBO->getId()) {
            throw new \Exception('User ID is required.');
        }

        if ($authBO->getEmail() && $isEmailTaken) {
            throw new \Exception('Email is already in use.');
        }

        if ($authBO->getUserType() && !in_array($authBO->getUserType(), [1, 2, 3])) {
            throw new \Exception('Invalid user type.');
        }
    }
}
