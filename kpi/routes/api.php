<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\KPIEntryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SupplierController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('customers', CustomerController::class);
Route::post('customers/restore/{id}', [CustomerController::class, 'restore']);

// Products CRUD
Route::apiResource('products', ProductController::class);
Route::post('products/restore/{id}', [ProductController::class, 'restore']); // Optional

// Suppliers CRUD
Route::apiResource('suppliers', SupplierController::class);
Route::post('suppliers/restore/{id}', [SupplierController::class, 'restore']); // Optional

// KPI Entries
Route::apiResource('kpis', KPIEntryController::class);
Route::post('kpi/bulk', [KPIEntryController::class, 'bulkStore']);
Route::put('/kpi/bulk-update', [KpiEntryController::class, 'bulkUpdate']);
Route::get('kpi/trashed', [KPIEntryController::class, 'trashed']);


// Assignments (custom pivot logic, if applicable)
Route::post('customer-products/{customerId}', [CustomerController::class, 'assignProducts']);
Route::get('customer-products/{customerId}', [CustomerController::class, 'getAssignedProducts']);
Route::delete('customer-products/{customerId}/{productId}', [CustomerController::class, 'removeProduct']);

Route::post('product-suppliers/{productId}', [ProductController::class, 'assignSuppliers']);
Route::get('product-suppliers/{productId}', [ProductController::class, 'getAssignedSuppliers']);
Route::delete('product-suppliers/{productId}/{supplierId}', [ProductController::class, 'removeSupplier']);
