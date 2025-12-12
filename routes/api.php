<?php

use App\Http\Controllers\api\ApiController;
use App\Http\Controllers\api\ObatMasterController;
use App\Http\Controllers\backend\PurchaseOrderController;
use App\Http\Controllers\backend\ReceivingController;
use App\Http\Controllers\backend\ShippingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function () {

    // ========== PURCHASE ORDER ==========
    Route::prefix('purchase-orders')->group(function () {
        // Get All PO
        Route::get('/', [PurchaseOrderController::class, 'index']);

        // Get Detail PO
        Route::get('/{id_po}', [PurchaseOrderController::class, 'show']);

        // APOTIK: Buat PO Internal ke Gudang
        Route::post('/internal', [PurchaseOrderController::class, 'createInternalPO']);
        Route::post('/internal/{id_po}/submit', [PurchaseOrderController::class, 'submitInternalPO']);

        // GUDANG: Buat PO Eksternal ke Supplier
        Route::post('/external', [PurchaseOrderController::class, 'createExternalPO']);
        Route::post('/external/{id_po}/submit', [PurchaseOrderController::class, 'submitExternalPO']);

        // KEPALA GUDANG: Approve/Reject
        Route::post('/{id_po}/approve-kepala-gudang', [PurchaseOrderController::class, 'approveByKepalaGudang']);

        // KASIR: Approve/Reject
        Route::post('/{id_po}/approve-kasir', [PurchaseOrderController::class, 'approveByKasir']);

        // Kirim ke Supplier
        Route::post('/{id_po}/send-to-supplier', [PurchaseOrderController::class, 'sendToSupplier']);
    });

    // ========== SHIPPING ACTIVITY ==========
    Route::prefix('shipping-activities')->group(function () {
        Route::post('/', [ShippingController::class, 'store']);
        Route::get('/po/{id_po}', [ShippingController::class, 'getByPO']);
    });

    // ========== RECEIVING ==========
    Route::post('/receiving/{id_po}', [ReceivingController::class, 'receiveGoods']);
});

Route::get('/obat-master-search', [ObatMasterController::class, 'search'])->name('api.obat-master.search');
Route::get('/asuransi/search', [ApiController::class, 'searchAsuransi'])->name('api.asuransi.search');

Route::get('/obat/search', [ApiController::class, 'searchObat'])->name('api.obat.search');
Route::get('/alkes/search', [ApiController::class, 'searchAlkes'])->name('api.alkes.search');
Route::get('/reagensia/search', [ApiController::class, 'searchReagensia'])->name('api.reagensia.search');
Route::get('/supplier/{id}/search-products', [ApiController::class, 'searchSupplierProducts'])->name('api.search.products');
