<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function getAll($fields){
        return User::select($fields)->latest()->paginate(10);
    }

    public function getById($id, $fields){
        return User::select($fields)->findOrFail($id);
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update($id, array $data)
    {
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        return $user->delete();
    }
}