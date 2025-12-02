<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;

class RoleRepository
{
    public function getAll($fields){
        return Role::select($fields)->latest()->paginate(10);
    }

    public function getById($id, $fields){
        return Role::select($fields)->findOrFail($id);
    }

    public function create(array $data)
    {
        return Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);
    }

    public function update($id, array $data)
    {
        $role = Role::findOrFail($id);
        $role->update($data);
        return $role;
    }

    public function delete($id)
    {
        $role = Role::findOrFail($id);
        return $role->delete();
    }
}