<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use App\Repositories\MerchantProductRepository;
use App\Repositories\ProductRepository;
use App\Repositories\MerchantRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    private TransactionRepository $transactionRepository;
    private MerchantProductRepository $merchantProductRepository;
    private ProductRepository $productRepository;
    private MerchantRepository $merchantRepository;

    public function __construct(
        TransactionRepository $transactionRepository,
        MerchantProductRepository $merchantProductRepository,
        ProductRepository $productRepository,
        MerchantRepository $merchantRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->merchantProductRepository = $merchantProductRepository;
        $this->productRepository = $productRepository;
        $this->merchantRepository = $merchantRepository;
    }

    public function getAll(array $fields)
    {
        return $this->transactionRepository->getAll($fields);
    }

    public function createTransaction(array $data)
    {
        DB::transaction(function () use ($data){
            $merchant = $this->merchantRepository->getById($data['merchant_id'], ['id', 'keeper_id']);

            if(!$merchant){
                throw ValidationException::withMessages(['merchant_id' => 'Merchant not found']);
            }

            if(Auth::id() !== $merchant->keeper_id){
                throw ValidationException::withMessages(['merchant_id' => 'You are not authorized to create transaction for this merchant']);
            }

            $products = [];
            $subtotal = 0;

            foreach ($data['products'] as $item) {
                $merchantProduct = $this->merchantProductRepository->getMerchantAndProduct($data['merchant_id'], $item['product_id']);

                if(!$merchantProduct || $merchantProduct->stock < $item['quantity']){
                    throw ValidationException::withMessages(['product_id' => 'Product with ID ' . $item['product_id'] . ' not found for this merchant.']);
                }

                $product = $this->productRepository->getById($item['product_id'], ['price']);

                if(!$product){
                    throw ValidationException::withMessages(['product_id' => 'Product with ID ' . $item['product_id'] . ' does not exist.']);
                }

                $price = $product->price;
                $productSubTotal = $price * $item['quantity'];
                $subtotal += $productSubTotal;

                $products[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'sub_total' => $productSubTotal,
                ];

                $newStock = max(0, $merchantProduct->stock - $item['quantity']);

                $this->merchantProductRepository->updateStock($data['merchant_id'], $item['product_id'], $newStock);

                $tax = $subtotal * 0.1;
                $grandTotal = $subtotal + $tax;

                $transaction = $this->transactionRepository->create([
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                    'merchant_id' => $data['merchant_id'],
                    'sub_total' => $subtotal,
                    'tax_total' => $tax,
                    'grand_total' => $grandTotal,

                ]);


                $this->transactionRepository->createTransactionProducts($transaction->id, $products);

                return $transaction->fresh();
            }
        });
    }

    public function getTransactionById(int $id, array $fields)
    {
        $trasaction = $this->transactionRepository->getById($id, $fields);
        if (!$trasaction) {
            throw ValidationException::withMessages(['id' => 'Transaction not found.']);
        }
        return $trasaction;
    }

    public function getTransactionByMerchant($merchantId){
        return $this->transactionRepository->getTrasactionByMerchant($merchantId);
    }
}
