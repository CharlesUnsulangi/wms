<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TguMsGudangController;
use App\Http\Controllers\TguMsProductBusinessController;
use App\Http\Controllers\TguTrInvMainMutasiController;
use App\Http\Controllers\TguMsRackInternalController;

// Main Dashboard
Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');

// Direct WMS Routes (No Prefix - Direct Connection)
// Gudang Management
Route::resource('gudang', TguMsGudangController::class);

// Product Business Management
Route::resource('product-business', TguMsProductBusinessController::class);

// Inventory Management
Route::resource('inventory', TguTrInvMainMutasiController::class);
Route::get('inventory/stock/summary', [TguTrInvMainMutasiController::class, 'getStockSummary'])->name('inventory.stock.summary');
Route::get('inventory/stock/movement', [TguTrInvMainMutasiController::class, 'getStockMovement'])->name('inventory.stock.movement');

// Rack Internal Management
Route::resource('rack-internal', TguMsRackInternalController::class);
Route::get('rack-internal/business/{business}', [TguMsRackInternalController::class, 'getByBusiness'])->name('rack-internal.by-business');
Route::get('rack-internal/branch/{branch}', [TguMsRackInternalController::class, 'getByBranch'])->name('rack-internal.by-branch');

// WMS Dashboard (Legacy route)
Route::get('wms/dashboard', function () {
    return view('wms.dashboard');
})->name('wms.dashboard');
