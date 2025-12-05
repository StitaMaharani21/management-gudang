<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('categories', \App\Http\Controllers\CategoryController::class);
Route::apiResource('products', \App\Http\Controllers\ProductController::class);
Route::apiResource('merchants', \App\Http\Controllers\MerchantController::class);
Route::apiResource('warehouses', \App\Http\Controllers\WarehouseController::class);

