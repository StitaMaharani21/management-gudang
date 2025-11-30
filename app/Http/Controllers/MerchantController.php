<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantRequest;
use App\Http\Resources\MerchantResource;
use App\Services\MerchantService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    private MerchantService $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    public function index(Request $request)
    {
        $fields = ['*'];
        $merchants = $this->merchantService->getAll($fields);
        return response()->json(MerchantResource::collection($merchants));
    }

    public function show($id)
    {
        try {
            $fields = ['*'];
            $merchant = $this->merchantService->getById($id, $fields);
            return response()->json(new MerchantResource($merchant));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }
    }

    public function store(MerchantRequest $request)
    {
        $data = $request->validated();
        $merchant = $this->merchantService->create($data);
        return response()->json(new MerchantResource($merchant), 201);
    }

    public function update(MerchantRequest $request, $id)
    {
        $data = $request->validated();
        try {
            $merchant = $this->merchantService->update($id, $data);
            return response()->json(new MerchantResource($merchant));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $this->merchantService->delete($id);
            return response()->json(['message' => 'Merchant deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }
    }

    public function getMyMerchantProfile()
    {
        $userId = Auth::id();
        try {
            $merchant = $this->merchantService->getByKeeperId($userId);
            return response()->json(new MerchantResource($merchant));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Merchant not found'], 404);
        }
    }
}
