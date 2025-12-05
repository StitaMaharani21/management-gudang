<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Services\WarehouseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    private WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function index()
    {
        $fields = ['*'];
        $warehouses = $this->warehouseService->getAll($fields ?: ['*']);

        return response()->json(WarehouseResource::collection($warehouses));
    }

    public function show($id)
    {
        try {
            $fields = ['*'];
            $warehouse = $this->warehouseService->getById($id, $fields);
            return response()->json(new WarehouseResource($warehouse));

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Warehouse not found.'], 404);
        }
    }


    public function store(WarehouseRequest $request)
    {
        $warehouse = $this->warehouseService->create($request->validated());

        return response()->json(new WarehouseResource($warehouse), 201);
    }

    public function update(WarehouseRequest $request, $id)
    {
        try {
            $warehouse = $this->warehouseService->update($request->validated(), $id);
            return response()->json(new WarehouseResource($warehouse));

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Warehouse not found.'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $this->warehouseService->delete($id);
            return response()->json(['message' => 'Warehouse deleted successfully.']);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Warehouse not found.'], 404);
        }
    }
}
