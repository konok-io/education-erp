<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Inventory\AssetController;
use App\Http\Controllers\Api\V1\Inventory\InventoryController;
use App\Http\Controllers\Api\V1\Inventory\PurchaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Asset, Inventory & Procurement Routes
|--------------------------------------------------------------------------
*/

Route::prefix('inventory')->middleware(['auth:sanctum'])->group(function () {

    // ===================== DASHBOARD =====================
    Route::get('/dashboard', [AssetController::class, 'getStats'])->name('inventory.dashboard');

    // ===================== ASSETS =====================
    Route::prefix('assets')->group(function () {
        Route::get('/', [AssetController::class, 'index'])->name('inventory.assets');
        Route::post('/', [AssetController::class, 'store'])->name('inventory.assets.store');
        Route::get('/categories', [AssetController::class, 'getCategories'])->name('inventory.assets.categories');
        Route::get('/stats', [AssetController::class, 'getStats'])->name('inventory.assets.stats');
        Route::get('/{uuid}', [AssetController::class, 'show'])->name('inventory.assets.show');
        Route::post('/{uuid}/assign', [AssetController::class, 'assign'])->name('inventory.assets.assign');
        Route::post('/{uuid}/return', [AssetController::class, 'returnAsset'])->name('inventory.assets.return');
        Route::post('/{uuid}/maintenance', [AssetController::class, 'scheduleMaintenance'])->name('inventory.assets.maintenance');
    });

    // ===================== INVENTORY ITEMS =====================
    Route::prefix('items')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory.items');
        Route::post('/', [InventoryController::class, 'store'])->name('inventory.items.store');
        Route::get('/categories', [InventoryController::class, 'getCategories'])->name('inventory.items.categories');
        Route::get('/locations', [InventoryController::class, 'getLocations'])->name('inventory.items.locations');
        Route::get('/stats', [InventoryController::class, 'getStats'])->name('inventory.items.stats');
        Route::get('/{uuid}', [InventoryController::class, 'show'])->name('inventory.items.show');
        Route::post('/{uuid}/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.items.adjust');
    });

    // ===================== STOCK TRANSFER =====================
    Route::prefix('transfers')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('inventory.transfers');
        Route::post('/', [InventoryController::class, 'transferStock'])->name('inventory.transfers.store');
    });

    // ===================== VENDORS =====================
    Route::prefix('vendors')->group(function () {
        Route::get('/', [PurchaseController::class, 'getVendors'])->name('inventory.vendors');
        Route::post('/', [PurchaseController::class, 'createVendor'])->name('inventory.vendors.store');
    });

    // ===================== PURCHASE REQUISITIONS =====================
    Route::prefix('requisitions')->group(function () {
        Route::get('/', [PurchaseController::class, 'getRequisitions'])->name('inventory.requisitions');
        Route::post('/', [PurchaseController::class, 'createRequisition'])->name('inventory.requisitions.store');
        Route::post('/{uuid}/submit', [PurchaseController::class, 'submitRequisition'])->name('inventory.requisitions.submit');
        Route::post('/{uuid}/approve', [PurchaseController::class, 'approveRequisition'])->name('inventory.requisitions.approve');
        Route::post('/{uuid}/convert', [PurchaseController::class, 'convertToPO'])->name('inventory.requisitions.convert');
    });

    // ===================== PURCHASE ORDERS =====================
    Route::prefix('orders')->group(function () {
        Route::get('/', [PurchaseController::class, 'getPurchaseOrders'])->name('inventory.orders');
        Route::get('/{uuid}', [PurchaseController::class, 'showPurchaseOrder'])->name('inventory.orders.show');
        Route::post('/{uuid}/receive', [PurchaseController::class, 'receiveGoods'])->name('inventory.orders.receive');
        Route::get('/stats', [PurchaseController::class, 'getStats'])->name('inventory.orders.stats');
    });
});
