<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseProductRequest;
use App\Http\Requests\WarehouseProductUpdateRequest;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\Request;

class WarehouseProductController extends Controller
{
    private WarehouseService $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        $this->warehouseService = $warehouseService;
    }

    public function attach(WarehouseProductRequest $request, $warehouseId)
    {
        $validated = $request->validated();
        $this->warehouseService->attachProduct(
            $warehouseId,
            $validated['product_id'],
            $validated['stock']
        );

        return response()->json(['message' => 'Product attached to warehouse successfully.'], 200);
    }

    public function detach($warehouseId, $productId)
    {
        $this->warehouseService->detachProduct($warehouseId, $productId);

        return response()->json(['message' => 'Product detached from warehouse successfully.'], 200);
    }

    public function update(WarehouseProductUpdateRequest $request, $warehouseId, $productId)
    {
        $warehouseProduct = $this->warehouseService->updateProductStock(
            $warehouseId,
            $productId,
            $request->validated()['stock']
        );

        return response()->json([
            'message' => 'Warehouse product stock updated successfully.',
            'data' => $warehouseProduct
        ], 200);
    }
}
