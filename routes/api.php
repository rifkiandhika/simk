<?php

use App\Http\Controllers\api\ApiController;
use App\Http\Controllers\api\ObatMasterController;
use App\Http\Controllers\backend\NotificationController;
use App\Http\Controllers\backend\PenjualanBebasController;
use App\Http\Controllers\backend\PenjualanResepController;
use App\Http\Controllers\backend\PinVerificationController;
use App\Http\Controllers\backend\PurchaseOrderController;
use App\Http\Controllers\backend\ReceivingController;
use App\Http\Controllers\backend\ReturController;
use App\Http\Controllers\backend\ReturDocumentController;
use App\Http\Controllers\backend\ReturReportController;
use App\Http\Controllers\backend\ShippingController;
use App\Http\Controllers\backend\StockapotikController;
use App\Http\Controllers\backend\TagihanPembayaranController;
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
    // ========================================
    // PURCHASE ORDER
    // ========================================
    Route::prefix('purchase-orders')->group(function () {
        Route::get('/', [PurchaseOrderController::class, 'index']);
        Route::get('/{id_po}', [PurchaseOrderController::class, 'show']);
        Route::post('/internal', [PurchaseOrderController::class, 'createInternalPO']);
        Route::post('/internal/{id_po}/submit', [PurchaseOrderController::class, 'submitInternalPO']);
        Route::post('/external', [PurchaseOrderController::class, 'createExternalPO']);
        Route::post('/external/{id_po}/submit', [PurchaseOrderController::class, 'submitExternalPO']);
        Route::post('/{id_po}/approve-kepala-gudang', [PurchaseOrderController::class, 'approveByKepalaGudang']);
        Route::post('/{id_po}/approve-kasir', [PurchaseOrderController::class, 'approveByKasir']);
        Route::post('/{id_po}/send-to-supplier', [PurchaseOrderController::class, 'sendToSupplier']);
    });

    Route::prefix('shipping-activities')->group(function () {
        Route::post('/', [ShippingController::class, 'store']);
        Route::get('/po/{id_po}', [ShippingController::class, 'getByPO']);
    });
    Route::post('/receiving/{id_po}', [ReceivingController::class, 'receiveGoods']);
});



// ========================================
// RETUR
// ========================================
    Route::post('/verify-pin', [PinVerificationController::class, 'verifyPin']);
  Route::prefix('purchase-orders')->group(function () {
        Route::get('/completed', [PurchaseOrderController::class, 'getCompleted']);
        Route::get('/{id}/items', [PurchaseOrderController::class, 'getItems']);
    });

    Route::prefix('stock-apotiks')->group(function () {
        Route::get('/', [StockApotikController::class, 'getAll']);
        Route::get('/{id}/items', [StockApotikController::class, 'getItems']);
    });

    Route::prefix('returs')->group(function () {
        Route::post('/available-items', [ReturController::class, 'getAvailableItems']);
    });
// ========================================
// OBAT
// ========================================
Route::get('/obat-master-search', [ObatMasterController::class, 'search'])->name('api.obat-master.search');

// ========================================
// API SEARCH DATA
// ========================================
Route::get('/asuransi/search', [ApiController::class, 'searchAsuransi'])->name('api.asuransi.search');
Route::get('/obat/search', [ApiController::class, 'searchObat'])->name('api.obat.search');
Route::get('/alkes/search', [ApiController::class, 'searchAlkes'])->name('api.alkes.search');
Route::get('/reagensia/search', [ApiController::class, 'searchReagensia'])->name('api.reagensia.search');
Route::get('/supplier/{id}/search-products', [ApiController::class, 'searchSupplierProducts'])->name('api.search.products');

Route::middleware(['auth:sanctum'])->group(function () {
    // Pembayaran
    Route::post('/tagihan/pembayaran', [TagihanPembayaranController::class, 'store']);
    Route::delete('/tagihan/pembayaran/{id}', [TagihanPembayaranController::class, 'destroy']);

    // Lock/Unlock
    Route::post('/tagihan/{id}/lock', [TagihanPembayaranController::class, 'lock']);
    Route::post('/tagihan/{id}/unlock', [TagihanPembayaranController::class, 'unlock'])
        ->middleware('role:admin,supervisor_keuangan');

    // Ringkasan
    Route::get('/tagihan/{id}/ringkasan', [TagihanPembayaranController::class, 'show']);
});
