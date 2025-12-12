<?php

use App\Http\Controllers\backend\AlkesController;
use App\Http\Controllers\backend\ApprovalController;
use App\Http\Controllers\backend\AsuransiController;
use App\Http\Controllers\backend\DepartmentController;
use App\Http\Controllers\backend\DetailObatrsController;
use App\Http\Controllers\backend\GudangController;
use App\Http\Controllers\backend\JenisController;
use App\Http\Controllers\backend\KaryawanController;
use App\Http\Controllers\backend\LoginController;
use App\Http\Controllers\backend\ObatMasterController;
use App\Http\Controllers\backend\ObatrsController;
use App\Http\Controllers\backend\PermintaanController;
use App\Http\Controllers\backend\PoConfirmationController;
use App\Http\Controllers\backend\PoexConfirmationController;
use App\Http\Controllers\backend\PurchaseOrderController;
use App\Http\Controllers\backend\ReagenController;
use App\Http\Controllers\backend\ReceivingController;
use App\Http\Controllers\backend\RolePermissionController;
use App\Http\Controllers\backend\SatuanController;
use App\Http\Controllers\backend\ShippingController;
use App\Http\Controllers\backend\StockapotikController;
use App\Http\Controllers\backend\SupplierController;
use App\Http\Controllers\backend\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ========================================
// PUBLIC ROUTES (No Authentication)
// ========================================

// Login Routes
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/forgot-password', [LoginController::class, 'forgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('send-reset-link');

// Redirect root to login if not authenticated
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// ========================================
// AUTHENTICATED ROUTES
// ========================================

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ========================================
    // SYSTEM MANAGEMENT
    // ========================================

    // User Management
    Route::resource('users', UserController::class);
    Route::patch('users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::patch('users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Role & Permission Management
    Route::resource('role-permissions', RolePermissionController::class);
    Route::post('role-permissions/assign', [RolePermissionController::class, 'assignRole'])->name('role-permissions.assign');
    Route::post('role-permissions/remove', [RolePermissionController::class, 'removeRole'])->name('role-permissions.remove');

    // ========================================
    // MASTER DATA
    // ========================================

    Route::resource('karyawans', KaryawanController::class);
    Route::resource('departments', DepartmentController::class);
    Route::resource('asuransis', AsuransiController::class);

    // ========================================
    // INVENTORY MANAGEMENT
    // ========================================

    // Suppliers
    Route::resource('suppliers', SupplierController::class);

    // Obat (Medicine)
    Route::resource('obat-masters', ObatMasterController::class);
    Route::resource('obatrs', ObatrsController::class);

    // Obat Detail Routes
    Route::prefix('obatrs/{obat}/detail/{detail}')->name('obat.detail.')->group(function () {
        Route::get('/edit', [DetailObatrsController::class, 'edit'])->name('edit');
        Route::post('/update-all', [DetailObatrsController::class, 'updateAll'])->name('update-all');
        Route::delete('/harga-asuransi/{id}', [DetailObatrsController::class, 'destroyHargaAsuransi'])->name('harga-asuransi.destroy');
        Route::post('/sync-kfa/{idObatMaster}', [DetailObatrsController::class, 'syncKFA'])->name('sync-kfa');
        Route::get('/kfa-history', [DetailObatrsController::class, 'getKFAHistory'])->name('kfa-history');
    });

    // Reagensia & Alkes
    Route::resource('reagens', ReagenController::class);
    Route::resource('alkes', AlkesController::class);

    // Jenis & Satuan
    Route::resource('jenis', JenisController::class);
    Route::resource('satuans', SatuanController::class);

    // Gudang
    Route::resource('gudangs', GudangController::class);
    Route::get('/gudang/barang/{barangId}/detail', [GudangController::class, 'getDetailGudangByBarang']);
    Route::post('/gudang/penerimaan', [GudangController::class, 'prosesPenerimaan'])->name('gudangs.penerimaan');
    Route::get('/gudang/{id}/details/data', [GudangController::class, 'detailsData']);
    Route::get('/supplier/{supplier}/details', [GudangController::class, 'getSupplierDetails']);

    // Gudang History & Stock
    Route::get('/history', [GudangController::class, 'historyGudang'])->name('gudang.history');
    Route::get('/stock', [GudangController::class, 'stockGudang'])->name('gudang.stock');


    Route::resource('stock_apotiks', StockapotikController::class);
    Route::get('/stock-apotik/supplier/{supplierId}/details', [StockapotikController::class, 'getSupplierProducts']);

    Route::resource('permintaans', PermintaanController::class);
    Route::post('permintaan/send/{id}', [PermintaanController::class, 'send'])->name('permintaans.send');
    Route::get('/permintaan/supplier/{supplierId}/details', [PermintaanController::class, 'getSupplierGudangDetails']);

    Route::resource('po', PurchaseOrderController::class)->parameters([
        'po' => 'id_po'
    ]);

    // Custom Routes untuk PO
    Route::prefix('po')->name('po.')->group(function () {
        // Submit PO untuk Approval
        Route::post('{id_po}/submit', [PurchaseOrderController::class, 'submit'])->name('submit');

        // Approval Kepala Gudang
        Route::post('{id_po}/approve-kepala-gudang', [PurchaseOrderController::class, 'approveKepalaGudang'])->name('approve.kepala-gudang');

        // Approval Kasir
        Route::post('{id_po}/approve-kasir', [PurchaseOrderController::class, 'approveKasir'])->name('approve.kasir');

        // Kirim ke Supplier
        Route::post('{id_po}/send-to-supplier', [PurchaseOrderController::class, 'sendToSupplier'])->name('send-to-supplier');

        // Print PO
        Route::get('{id_po}/print', [PurchaseOrderController::class, 'print'])->name('print');
        // Print Invoice
        Route::get('/po/{id_po}/print-invoice', [PurchaseOrderController::class, 'printInvoice'])->name('print-invoice');

        // Internal
        Route::get('{id_po}/confirm-receipt', [PoConfirmationController::class, 'showConfirmation'])
            ->name('show-confirmation');
        Route::post('{id_po}/confirm-receipt', [PoConfirmationController::class, 'confirmReceipt'])
            ->name('confirm-receipt');
        // External
        Route::get('{id_po}/confirm-receipt', [PoexConfirmationController::class, 'showConfirmation'])
            ->name('show-confirmation');
        Route::post('{id_po}/confirm-receipt', [PoexConfirmationController::class, 'confirmReceipt'])
            ->name('confirm-receipt');
        Route::get('/po/{id_po}/invoice-form', [PoexConfirmationController::class, 'showInvoiceForm'])
            ->name('invoice-form');

        Route::post('/po/{id_po}/store-invoice', [PoexConfirmationController::class, 'storeInvoice'])
            ->name('store-invoice');
    });


    // Resource Route untuk Shipping
    Route::resource('shipping', ShippingController::class);

    Route::prefix('shipping')->name('shipping.')->group(function () {

        // Halaman index tracking pengiriman
        Route::get('/', [ShippingController::class, 'index'])
            ->name('by-po');

        // Store shipping activity (digunakan dari modal)
        Route::post('/store', [ShippingController::class, 'store'])
            ->name('store');

        // Get shipping activities by PO ID
        Route::get('/po/{id_po}', [ShippingController::class, 'getByPO'])
            ->name('getByPO');
    });
    Route::resource('stock_apotiks', StockApotikController::class);

    // Custom route untuk mengambil detail gudang berdasarkan gudang_id
    Route::get('/stock-apotik/gudang/{gudangId}/details', [StockApotikController::class, 'getGudangDetails'])
        ->name('stock_apotiks.gudang.details');

    // Optional: Export routes jika diperlukan
    Route::get('/stock_apotiks/export/excel', [StockApotikController::class, 'exportExcel'])
        ->name('stock_apotiks.export.excel');

    Route::get('/stock_apotiks/export/pdf', [StockApotikController::class, 'exportPdf'])
        ->name('stock_apotiks.export.pdf');

    // Route::get('stock-apotik/test-connection/{gudangId}', [StockApotikController::class, 'testConnection']);
    // Route::get('/test-gudang-simple/{id}', function ($id) {
    //     return response()->json([
    //         'gudang_id' => $id,
    //         'message' => 'Gudang ID received'
    //     ]);
    // });
});


Route::fallback(function () {
    return view('errors.404');
});
