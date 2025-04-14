<?php


namespace App\Repository;


use App\Models\User;
use App\Repository\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface {

    
    public function getWalletBalance(int $userId): float
    {
        return User::where('id', $userId)->value('wallet_amount');
    }

    public function updateWalletBalance(string $userId, float $newBalance)
    {
        return User::where('id', $userId)->update([
            'wallet_amount' => $newBalance
        ]);
    }





    public function getAll()
    {
        return User::all();
    }

    public function findById($userId)
    {
        return User::findOrFail($userId);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($userId, array $data)
    {
        $user = $this->findById($userId);
        $user->update($data);
        return $user;
    }
    
    public function updateRole($userId, array $data)
    {
        $user = $this->findById($userId);
        $user->update($data);
        return $user;
    }

    public function delete($userId, array $data)
    {
        $user = $this->findById($userId);
        $user->delete();
        return $user;
    }

}
