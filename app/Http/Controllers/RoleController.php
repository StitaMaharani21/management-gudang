<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use App\Services\RoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleController extends Controller
{
    private RoleService $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function index()
    {
        $fields = ['id', 'name'];
        $roles = $this->roleService->getAll($fields);
        return response()->json(RoleResource::collection($roles), 200);
    }

    public function show($id)
    {
        $fields = ['id', 'name'];
        $role = $this->roleService->getById($id, $fields);
        return response()->json(new RoleResource($role), 200);
    }

    public function store(RoleRequest $request)
    {
        $data = $request->validated();
        $role = $this->roleService->create($data);
        return response()->json(new RoleResource($role), 201);
    }

    public function update(RoleRequest $request, $id)
    {
        $data = $request->validated();
        $role = $this->roleService->update($id, $data);
        return response()->json(new RoleResource($role), 200);
    }

    public function destroy($id)
    {
        try {
            $this->roleService->delete($id);
            return response()->json(['message' => 'Role deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Role not found'], 404);
        }
    }
}
