<?php

namespace App\Services;

use App\Repositories\RoleRepository;


class RoleService
{
    private RoleRepository $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getAll($fields)
    {
        return $this->roleRepository->getAll($fields);
    }

    public function getById($id, $fields)
    {
        return $this->roleRepository->getById($id, $fields ?? ['*']);
    }

    public function create(array $data)
    {
        return $this->roleRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->roleRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->roleRepository->delete($id);
    }
}