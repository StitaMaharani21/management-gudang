<?php

namespace App\Services;

use App\Repositories\WarehouseRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WarehouseService
{
    private WarehouseRepository $warehouseRepository;

    public function __construct(WarehouseRepository $warehouseRepository)
    {
        $this->warehouseRepository = $warehouseRepository;
    }

    public function getAll(array $fields)
    {
        return $this->warehouseRepository->getAll($fields);
    }

    public function getById($id, array $fields)
    {
        return $this->warehouseRepository->getById($id, $fields);
    }

    public function create(array $data)
    {
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->warehouseRepository->create($data);
    }

    public function update(array $data, $id)
    {
        $fields = ['id'];
        $warehouse = $this->warehouseRepository->getById($id, $fields);
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            if (!empty($warehouse->photo)) {
                $this->deletePhoto($warehouse->photo);
            }
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }
        return $this->warehouseRepository->update($id, $data);
    }

    public function attachProduct($warehouseId, $productId, $stock)
    {
        $warehouse = $this->warehouseRepository->getById($warehouseId, ['id']);
        $warehouse->products()->syncWithoutDetaching([
            $productId => ['stock' => $stock]
        ]);
    }

    public function detachProduct($warehouseId, $productId)
    {
        $warehouse = $this->warehouseRepository->getById($warehouseId, ['id']);
        $warehouse->products()->detach($productId);
    }


    public function updateProductStock($warehouseId, $productId, $stock)
    {
        $warehouse = $this->warehouseRepository->getById($warehouseId, ['id']);
        $warehouse->products()->updateExistingPivot($productId, ['stock' => $stock]);

        return $warehouse->products()->where('product_id', $productId)->first();
    }


    public function delete($id)
    {
        $fields = ['id', 'photo'];

        $warehouse = $this->warehouseRepository->getById($id, $fields);

        if (!empty($warehouse->photo)) {
            $this->deletePhoto($warehouse->photo);
        }
    }

    private function uploadPhoto(UploadedFile $photo)
    {
        return $photo->store('warehouses', 'public');
    }

    private function deletePhoto($photoPath)
    {
        $relativePath = 'warehouses/' . basename($photoPath);
        if (Storage::disk('public')->exists($relativePath)) {
            Storage::disk('public')->delete($relativePath);
        }
    }
}
