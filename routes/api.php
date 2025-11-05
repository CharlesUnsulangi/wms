<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TguMsGudangController;
use App\Http\Controllers\TguMsProductBusinessController;
use App\Http\Controllers\TguTrInvMainMutasiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// WMS API Routes
Route::prefix('wms')->group(function () {
    // Gudang API
    Route::apiResource('gudang', TguMsGudangController::class);
    
    // Product Business API
    Route::apiResource('product-business', TguMsProductBusinessController::class);
    
    // Inventory API
    Route::apiResource('inventory', TguTrInvMainMutasiController::class);
    Route::get('inventory/stock/summary', [TguTrInvMainMutasiController::class, 'getStockSummary']);
    Route::get('inventory/stock/movement', [TguTrInvMainMutasiController::class, 'getStockMovement']);
    Route::get('inventory/analytics/dashboard', [TguTrInvMainMutasiController::class, 'getDashboardAnalytics']);
    
    // Custom API routes
    Route::get('gudang/business/{business}', [TguMsGudangController::class, 'getByBusiness']);
    Route::get('product-business/category/{category}', [TguMsProductBusinessController::class, 'getByCategory']);
});