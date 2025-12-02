<?php

namespace App\Services;

use App\Repositories\UserRoleRepository;

class UserRoleService
{
    private UserRoleRepository $userRoleRepository;

    public function __construct(UserRoleRepository $userRoleRepository)
    {
        $this->userRoleRepository = $userRoleRepository;
    }

    public function assignRoleToUser($userId, $roleId)
    {
        return $this->userRoleRepository->assignRoleToUser($userId, $roleId);
    }

    public function removeUserRole($userId, $roleId)
    {
        return $this->userRoleRepository->removeUserRole($userId, $roleId);
    }

    public function getUserRoles($userId)
    {
        return $this->userRoleRepository->getUserRoles($userId);
    }
}