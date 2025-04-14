<?php

namespace App\Repository\Interfaces;


interface UserRepositoryInterface
{
    public function getWalletBalance(int $userId): float;
    public function updateWalletBalance(string $userId, float $newBalance);








    public function getAll();
    public function findById($userId);
    public function create(array $data);
    public function update($userId, array $data);
    public function updateRole($userId, array $data);
    public function delete($userId, array $data);
}
