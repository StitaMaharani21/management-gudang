<?php

namespace App\Repositories;

use App\Models\MerchantProduct;
use Illuminate\Validation\ValidationException;

class MerchantProductRepository
{
    public function create(array $data)
    {
        return MerchantProduct::create($data);
    }

    public function getMerchantAndProduct($merchantId, $productId)
    {
        return MerchantProduct::where('merchant_id', $merchantId)
            ->where('product_id', $productId)
            ->first();
    }

    public function updateStock($merchantProductId, $productId, $stock)
    {
        $merchantProduct = $this->getMerchantAndProduct($merchantProductId, $productId);
        if (!$merchantProduct) {
            throw ValidationException::withMessages(['product_id' => 'Merchant product not found.']);
        }

        $merchantProduct->update(['stock' => $stock]);
        return $merchantProduct;
    }
}
