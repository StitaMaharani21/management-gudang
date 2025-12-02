<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRoleRequest;
use App\Services\UserRoleService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    private UserRoleService $userRoleService;

    public function __construct(UserRoleService $userRoleService)
    {
        $this->userRoleService = $userRoleService;
    }

    public function assignRole(UserRoleRequest $request)
    {
        $data = $request->validated();
        $user = $this->userRoleService->assignRoleToUser($data['user_id'], $data['role_id']);
        return response()->json(['message' => 'Role assigned successfully', 'user' => $user], 200);
    }

    public function removeRole(UserRoleRequest $request)
    {
        $data = $request->validated();
        $user = $this->userRoleService->removeUserRole($data['user_id'], $data['role_id']);
        return response()->json(['message' => 'Role removed successfully', 'user' => $user], 200);
    }

    public function getUserRoles($userId)
    {
        try {
            $roles = $this->userRoleService->getUserRoles($userId);
            return response()->json(['user_id' => $userId, 'roles' => $roles], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }
}
