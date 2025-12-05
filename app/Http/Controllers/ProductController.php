<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $fields = ['*'];
        $products = $this->productService->getAll($fields);
        return response()->json(ProductResource::collection($products));
    }

    public function show($id)
    {
        try {
            $fields = ['*'];
            $product = $this->productService->getById($id, $fields);
            return response()->json(new ProductResource($product));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found.'], 404);
        }
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        $product = $this->productService->create($data);
        return response()->json(new ProductResource($product), 201);
    }

    public function update(ProductRequest $request, $id)
    {
        $data = $request->validated();
        try {
            $product = $this->productService->update($data, $id);
            return response()->json(new ProductResource($product));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found.'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $this->productService->delete($id);
            return response()->json(['message' => 'Product deleted successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Product not found.'], 404);
        }
    }
}
