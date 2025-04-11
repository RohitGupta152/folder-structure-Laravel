<?php

namespace App\Repository\Interfaces;

interface AuthRepositoryInterface
{
    public function create(array $data): array;
    public function checkEmailExists(string $email);

    public function getUserByEmail(string $email);

    public function update(int $id, array $data): bool;
    public function getUserById(int $id);
    public function checkEmailExistsForOtherUser(string $email, int $excludeId);
}
