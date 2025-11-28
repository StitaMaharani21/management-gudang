<?php

namespace App\Repositories;

use App\Models\WarehouseProduct;
use Illuminate\Validation\ValidationException;

class WarehouseProductRepository
{
    public function getWarehouseProducts($warehouseId, $producctId)
    {
        return WarehouseProduct::where('warehouse_id', $warehouseId)
            ->where('product_id', $producctId)
            ->first();
    }

    public function updateStock($warehouseId, $productId, $newStock)
    {
        $warehouseProduct = $this->getWarehouseProducts($warehouseId, $productId);

        if(!$warehouseProduct) {
            throw ValidationException::withMessages(['product_id' => 'Warehouse product not found.']);
        }

        $warehouseProduct->update(['stock' => $newStock]);
        return $warehouseProduct;
    }
}