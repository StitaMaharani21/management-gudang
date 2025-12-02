<?php

namespace App\Repositories;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleRepository
{
    public function assignRoleToUser($userId, $roleId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);
        $user->assignRole($role);
        return $user;
    } 


    public function removeUserRole($userId, $roleId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);
        $user->removeRole($role);
        return $user;
    }

    public function getUserRoles($userId)
    {
        $user = User::findOrFail($userId);
        return $user->roles;
    }
}