<?php

namespace App\Repositories;

use App\Models\Warehouse;

class WarehouseRepository
{
    public function getAll(array $fields){
        return Warehouse::select($fields)->with(['products.category'])->latest()->paginate(10);
    }

    public function getById($id, array $fields){
        return Warehouse::select($fields)->with(['products.category'])->findOrFail($id);
    }

    public function create(array $data){
        return Warehouse::create($data);
    }

    public function update($id, array $data){
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->update($data);
        return $warehouse;
    }

    public function delete($id){
        $warehouse = Warehouse::findOrFail($id);
        return $warehouse->delete();
    }
}