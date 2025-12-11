<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('token-login', [\App\Http\Controllers\AuthController::class, 'tokenLogin']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout']);
    Route::get('user', [\App\Http\Controllers\AuthController::class, 'user']);
});

Route::middleware('auth:sanctum', 'role:manager')->group(function () {
    Route::apiResource('categories', \App\Http\Controllers\CategoryController::class);
    Route::apiResource('products', \App\Http\Controllers\ProductController::class);
    Route::apiResource('merchants', \App\Http\Controllers\MerchantController::class);
    Route::apiResource('warehouses', \App\Http\Controllers\WarehouseController::class);
    Route::apiResource('roles', \App\Http\Controllers\RoleController::class);
    Route::apiResource('users', \App\Http\Controllers\UserController::class);
    Route::post('users/roles', [\App\Http\Controllers\UserRoleController::class, 'assignRole']);



    Route::post('warehouses/{warehouse}/products', [\App\Http\Controllers\WarehouseProductController::class, 'attach']);
    Route::delete('warehouses/{warehouse}/products/{product}', [\App\Http\Controllers\WarehouseProductController::class, 'detach']);
    Route::put('warehouses/{warehouse}/products/{product}', [\App\Http\Controllers\WarehouseProductController::class, 'update']);


    Route::post('merchants/{merchant}/products', [\App\Http\Controllers\MerchantProductController::class, 'store']);
    Route::put('merchants/{merchant}/products/{product}', [\App\Http\Controllers\MerchantProductController::class, 'update']);
    Route::delete('merchants/{merchant}/products/{product}', [\App\Http\Controllers\MerchantProductController::class, 'destroy']);

    Route::get('transactions', [\App\Http\Controllers\TransactionController::class, 'getAll']);
    Route::post('transactions', [\App\Http\Controllers\TransactionController::class, 'store']);
    Route::get('transactions/{transaction}', [\App\Http\Controllers\TransactionController::class, 'show']);
    Route::get('my-merchant', [\App\Http\Controllers\MerchantController::class, 'getMyMerchantProfile']);
    Route::get('my-merchant/transactions', [\App\Http\Controllers\TransactionController::class, 'getTransactionByMerchant']);

});


Route::middleware('auth:sanctum', 'role:manager|keeper')->group(function () {

    Route::get('categories', [\App\Http\Controllers\CategoryController::class, 'index']);
    Route::get('categories/{category}', [\App\Http\Controllers\CategoryController::class, 'show']);
    Route::get('products', [\App\Http\Controllers\ProductController::class, 'index']);
    Route::get('products/{product}', [\App\Http\Controllers\ProductController::class, 'show']);
    Route::get('warehouses', [\App\Http\Controllers\WarehouseController::class, 'index']);
    Route::get('warehouses/{warehouse}', [\App\Http\Controllers\WarehouseController::class, 'show']);



    Route::post('transactions', [\App\Http\Controllers\TransactionController::class, 'store']);
    Route::get('transactions/{transaction}', [\App\Http\Controllers\TransactionController::class, 'show']);
    Route::get('my-merchant', [\App\Http\Controllers\MerchantController::class, 'getMyMerchantProfile']);
    Route::get('my-merchant/transactions', [\App\Http\Controllers\TransactionController::class, 'getTransactionByMerchant']);
});
