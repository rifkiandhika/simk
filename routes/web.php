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
use App\Http\Controllers\backend\NotificationController;
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
use App\Http\Controllers\backend\TagihanPoController;
use App\Http\Controllers\backend\UserController;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    // Notifications

    Route::get('/notifications', [NotificationController::class, 'getNotifications']);

    // Mark as read
    Route::post('/notifications/read', [NotificationController::class, 'markAsRead']);

    // Trigger auto-cancel manually (for testing)
    Route::post('/notifications/auto-cancel', [NotificationController::class, 'autoCancelPendingPO']);


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
        Route::get('{id_po}/confirm-receipt-internal', [PoConfirmationController::class, 'showConfirmation'])
            ->name('show-confirmation');
        Route::post('{id_po}/confirm-receipt-internal', [PoConfirmationController::class, 'confirmReceipt'])
            ->name('confirm-receipt');
        // External
        Route::get('{id_po}/confirm-receipt-external', [PoexConfirmationController::class, 'showConfirmation'])
            ->name('showex-confirmation');
        Route::post('{id_po}/confirm-receipt-external', [PoexConfirmationController::class, 'confirmReceipt'])
            ->name('confirmex-receipt');
        Route::get('/po/{id_po}/invoice-form', [PoexConfirmationController::class, 'showInvoiceForm'])
            ->name('invoice-form');

        Route::post('/po/{id_po}/store-invoice', [PoexConfirmationController::class, 'storeInvoice'])
            ->name('store-invoice');
    });


    Route::prefix('tagihan')->name('tagihan.')->middleware(['auth'])->group(function () {
        // List & Detail
        Route::get('/', [TagihanPoController::class, 'index'])->name('index');
        Route::get('/{id_tagihan}', [TagihanPoController::class, 'show'])->name('show');

        // Payment
        Route::get('/{id_tagihan}/payment', [TagihanPoController::class, 'showPaymentForm'])->name('payment.form');
        Route::post('/{id_tagihan}/payment', [TagihanPoController::class, 'processPayment'])->name('payment.process');
        Route::get('/{id_tagihan}/payment-history', [TagihanPoController::class, 'paymentHistory'])->name('payment.history');

        // Verify Payment (Manager/Supervisor)
        Route::post('/payment/{id_pembayaran}/verify', [TagihanPoController::class, 'verifyPayment'])->name('payment.verify');

        // Download & Print
        Route::get('/payment/{id_pembayaran}/download', [TagihanPoController::class, 'downloadBukti'])->name('payment.download');
        Route::get('/{id_tagihan}/print', [TagihanPoController::class, 'print'])->name('print');
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

Route::get('/debug/po-status', function () {
    $cutoffTime = Carbon::now()->subHours(24);

    $pendingPOs = PurchaseOrder::whereIn('status', [
        'menunggu_persetujuan_kepala_gudang',
        'menunggu_persetujuan_kasir'
    ])->get();

    $result = [
        'current_time' => Carbon::now()->format('Y-m-d H:i:s'),
        'cutoff_time' => $cutoffTime->format('Y-m-d H:i:s'),
        'total_pending' => $pendingPOs->count(),
        'should_be_cancelled' => [],
        'still_pending' => [],
    ];

    foreach ($pendingPOs as $po) {
        $hoursAgo = $po->created_at->diffInHours(now());
        $shouldCancel = $po->created_at->lte($cutoffTime);

        $poData = [
            'no_po' => $po->no_po,
            'status' => $po->status,
            'created_at' => $po->created_at->format('Y-m-d H:i:s'),
            'hours_ago' => $hoursAgo,
            'should_cancel' => $shouldCancel,
        ];

        if ($shouldCancel) {
            $result['should_be_cancelled'][] = $poData;
        } else {
            $result['still_pending'][] = $poData;
        }
    }

    return response()->json($result, 200, [], JSON_PRETTY_PRINT);
});

Route::get('/debug/run-auto-cancel', function () {
    try {
        Artisan::call('po:auto-cancel');
        $output = Artisan::output();

        return response()->json([
            'success' => true,
            'message' => 'Command executed successfully',
            'output' => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});

Route::get('/debug/scheduler-info', function () {
    $schedule = app()->make(\Illuminate\Console\Scheduling\Schedule::class);

    $events = collect($schedule->events())->map(function ($event) {
        return [
            'command' => $event->command ?? $event->description,
            'expression' => $event->expression,
            'timezone' => $event->timezone,
            'next_run' => $event->nextRunDate()->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'scheduled_tasks' => $events,
        'server_time' => now()->format('Y-m-d H:i:s'),
        'timezone' => config('app.timezone'),
    ], 200, [], JSON_PRETTY_PRINT);
});

// Route untuk create dummy PO (untuk testing)
Route::get('/debug/create-old-po', function () {
    if (!app()->environment('local')) {
        abort(403, 'Only available in local environment');
    }

    try {
        $po = PurchaseOrder::create([
            'tipe_po' => 'internal',
            'status' => 'menunggu_persetujuan_kepala_gudang',
            'id_unit_pemohon' => '123e4567-e89b-12d3-a456-426614174000', // Sesuaikan dengan data Anda
            'unit_pemohon' => 'apotik',
            'id_karyawan_pemohon' => '123e4567-e89b-12d3-a456-426614174001', // Sesuaikan dengan data Anda
            'tanggal_permintaan' => Carbon::now()->subHours(25), // 25 jam yang lalu
            'catatan_pemohon' => 'Testing PO - Auto cancel',
            'unit_tujuan' => 'gudang',
            'total_harga' => 100000,
            'pajak' => 10000,
            'grand_total' => 110000,
            'tanggal_jatuh_tempo' => now()->addDays(30),
        ]);

        // Paksa update created_at (untuk testing)
        DB::table('purchase_orders')
            ->where('id_po', $po->id_po)
            ->update(['created_at' => Carbon::now()->subHours(25)]);

        return response()->json([
            'success' => true,
            'message' => 'Dummy PO created',
            'po' => $po->fresh(),
            'hours_ago' => $po->fresh()->created_at->diffInHours(now())
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});
