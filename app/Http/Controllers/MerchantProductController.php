<?php

namespace App\Http\Controllers;

use App\Http\Requests\MerchantProductRequest;
use App\Http\Requests\MerchantProductUpdateRequest;
use App\Services\MerchantProductService;
use Illuminate\Http\Request;

class MerchantProductController extends Controller
{
    private MerchantProductService $merchantProductService;

    public function __construct(MerchantProductService $merchantProductService)
    {
        $this->merchantProductService = $merchantProductService;
    }


    public function store(MerchantProductRequest $request, $merchant)
    {
        $validated = $request->validated();
        $validated['merchant_id'] = $merchant;

        $merchantProduct = $this->merchantProductService->assignProductToMerchant($validated);

        return response()->json([
            'message' => 'Product assigned to merchant successfully.',
            'data' => $merchantProduct,
        ], 201);
    }

    public function update(MerchantProductUpdateRequest $request, $merchantId, $productId)
    {
        $validated = $request->validated();

        $merchantProduct = $this->merchantProductService->updateStock(
            $merchantId,
            $productId,
            $validated['stock'],
            $validated['warehouse_id']
        );

        return response()->json([
            'message' => 'Merchant product stock updated successfully.',
            'data' => $merchantProduct,
        ]);
    }

    public function destroy($merchant, $product){
        $this->merchantProductService->removeProductFromMerchant($merchant, $product);

        return response()->json([
            'message' => 'Merchant product removed successfully.',
        ]);
    }
}
