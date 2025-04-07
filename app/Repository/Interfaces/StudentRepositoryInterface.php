<?php


namespace App\Repository\Interfaces;

use Illuminate\Support\Collection;

interface StudentRepositoryInterface
{
    public function checkEmailExists(string $email);
    public function create(array $data): array;

    public function checkEmailExistsForOther(string $email, int $excludeId);
    public function findById(int $id): ?array;
    public function update(array $existingStudent, array $data): ?array;

    public function deleteById(int $id): bool;

    public function getStudent(array $filters);
}
