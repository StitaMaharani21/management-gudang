<?php

namespace App\Services;

use App\Repositories\MerchantProductRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\WarehouseProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MerchantProductService
{
    private MerchantProductRepository $merchantProductRepository;
    private WarehouseProductRepository $warehouseProductRepository;
    private MerchantRepository $merchantRepository;

    public function __construct(MerchantProductRepository $merchantProductRepository, WarehouseProductRepository $warehouseProductRepository, MerchantRepository $merchantRepository)
    {
        $this->merchantProductRepository = $merchantProductRepository;
        $this->warehouseProductRepository = $warehouseProductRepository;
        $this->merchantRepository = $merchantRepository;
    }

    public function assignProductToMerchant(array $data)
    {
        return DB::transaction(function () use ($data) {
            //untuk cek data
            $warehouseProduct = $this->warehouseProductRepository->getWarehouseProducts($data['warehouse_id'], $data['product_id']);

            if (!$warehouseProduct || $warehouseProduct->stock <= $data['stock']) {
                throw ValidationException::withMessages(['stock' => 'Insufficient stock in warehouse product.']);
            }

            $existingProduct = $this->merchantProductRepository->getMerchantAndProduct($data['merchant_id'], $data['product_id']);

            if ($existingProduct) {
                throw ValidationException::withMessages(['product' => 'Product already assigned to the merchant.']);
            }

            //kurangi stock di warehouse product
            $this->warehouseProductRepository->updateStock(
                $data['warehouse_id'],
                $data['product_id'],
                $warehouseProduct->stock - $data['stock']
            );

            //create merchant product
            return $this->merchantProductRepository->create([
                'merchant_id' => $data['merchant_id'],
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'stock' => $data['stock'],
            ]);
        });
    }

    public function updateStock($merchantId, $productId, $newStock, $warehouseId)
    {
        return DB::transaction(function () use ($merchantId, $productId, $newStock, $warehouseId) {
            $merchantProduct = $this->merchantProductRepository->getMerchantAndProduct($merchantId, $productId);

            if (!$merchantProduct) {
                throw ValidationException::withMessages(['product' => 'Product not found for the merchant.']);
            }

            if(!$warehouseId) {
                throw ValidationException::withMessages(['warehouse_id' => 'Warehouse ID is required.']);
            }

            //stock product yang ada di merchant

            $currentStock = $merchantProduct->stock;

            if($newStock > $currentStock){
                $diff = $newStock - $currentStock;
                $warehouseProduct = $this->warehouseProductRepository->getWarehouseProducts($warehouseId, $productId);

                if (!$warehouseProduct || $warehouseProduct->stock < $diff) {
                    throw ValidationException::withMessages(['stock' => 'Insufficient stock in warehouse product.']);
                }

                //kurangi stock di warehouse product
                $this->warehouseProductRepository->updateStock(
                    $warehouseId,
                    $productId,
                    $warehouseProduct->stock - $diff
                );
            }

            if($newStock < $currentStock){
                $diff = $currentStock - $newStock;
                $warehouseProduct = $this->warehouseProductRepository->getWarehouseProducts($warehouseId, $productId);

                if (!$warehouseProduct) {
                    throw ValidationException::withMessages(['warehouse_product' => 'Warehouse product not found.']);
                }

                //tambah stock di warehouse product
                $this->warehouseProductRepository->updateStock(
                    $warehouseId,
                    $productId,
                    $warehouseProduct->stock + $diff
                );
            }

            //update stock di merchant product
            return $this->merchantProductRepository->updateStock(
                $merchantId,
                $productId,
                $newStock
            );
        });
    }

    public function removeProductFromMerchant($merchantId, $productId)
    {
        $merchant = $this->merchantRepository->getById($merchantId, $productId);

        if (!$merchant) {
            throw ValidationException::withMessages(['merchant' => 'Merchant not found.']);
        }

        $exists = $this->merchantProductRepository->getMerchantAndProduct($merchantId, $productId);

        if (!$exists) {
            throw ValidationException::withMessages(['product' => 'Product not found for the merchant.']);
        }

        $merchant->products()->detach($productId);
    }
}
