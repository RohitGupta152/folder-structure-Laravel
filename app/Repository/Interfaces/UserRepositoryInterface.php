<?php

namespace App\Repository\Interfaces;


interface UserRepositoryInterface
{
    public function getWalletBalance(int $userId): float;
    public function updateWalletBalance(string $userId, float $newBalance);
}
