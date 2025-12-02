<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getAll($fields){
        return Product::select($fields)->with('category')->latest()->paginate(10);
    }

    public function getById($id, $fields){
        return Product::select($fields)->with('category')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Product::create($data);
    }


    public function update($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        return $product->delete();
    }
}