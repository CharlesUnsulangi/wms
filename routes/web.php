<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TguMsGudangController;
use App\Http\Controllers\TguMsProductBusinessController;
use App\Http\Controllers\TguTrInvMainMutasiController;

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// WMS Routes
Route::prefix('wms')->group(function () {
    // Gudang Management
    Route::resource('gudang', TguMsGudangController::class);
    
    // Product Business Management
    Route::resource('product-business', TguMsProductBusinessController::class);
    
    // Inventory Management
    Route::resource('inventory', TguTrInvMainMutasiController::class);
    Route::get('inventory/stock/summary', [TguTrInvMainMutasiController::class, 'getStockSummary'])->name('inventory.stock.summary');
    Route::get('inventory/stock/movement', [TguTrInvMainMutasiController::class, 'getStockMovement'])->name('inventory.stock.movement');
    
    // Dashboard WMS
    Route::get('/dashboard', function () {
        return view('wms.dashboard');
    })->name('wms.dashboard');
});
