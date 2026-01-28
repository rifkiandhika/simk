<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\DetailGudang;
use App\Models\DetailstockApotik;
use App\Models\Retur;
use App\Models\ReturItem;
use App\Models\ReturItemBatch;
use App\Models\ReturHistory;
use App\Models\PurchaseOrder;
use App\Models\StockApotik;
use App\Models\Supplier;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class ReturController extends Controller
{
    /**
     * Display a listing of returs
     */
    public function index(Request $request)
    {
        $query = Retur::with([
            'karyawanPelapor',
            'returItems',
            'supplier'
        ]);

        // Filter berdasarkan tipe
        if ($request->has('tipe_retur')) {
            $query->where('tipe_retur', $request->tipe_retur);
        }

        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->has('tanggal_dari') && $request->has('tanggal_sampai')) {
            $query->whereBetween('tanggal_retur', [
                $request->tanggal_dari,
                $request->tanggal_sampai
            ]);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_retur', 'like', "%{$search}%")
                    ->orWhere('kode_referensi', 'like', "%{$search}%");
            });
        }

        $returs = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('returs.index', compact('returs'));
    }

    /**
     * Show the form for creating a new retur
     */
    public function create()
    {
        $suppliers = Supplier::where('status', 'aktif')->get();
        return view('returs.create', compact('suppliers'));
    }

    /**
     * Store a newly created retur
     */
    public function store(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:6',
            'id_karyawan_pelapor' => 'required|uuid',
            'tipe_retur' => 'required|in:po,stock_apotik',
            'id_sumber' => 'required|uuid',
            'alasan_retur' => 'required|in:barang_rusak,barang_kadaluarsa,barang_tidak_sesuai,kelebihan_pengiriman,kesalahan_order,kualitas_tidak_baik,lainnya',
            'keterangan_alasan' => 'nullable|string',
            'tanggal_retur' => 'required|date',
            'unit_pelapor' => 'required|in:apotik,gudang',
            'unit_tujuan' => 'nullable|in:gudang,supplier',
            'id_supplier' => 'nullable|uuid',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.id_item_sumber' => 'required|uuid',
            'items.*.id_produk' => 'required|uuid',
            'items.*.nama_produk' => 'required|string',
            'items.*.qty_diretur' => 'required|integer|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.kondisi_barang' => 'required|in:baik,rusak,kadaluarsa',
        ]);

        DB::beginTransaction();
        try {
            // Verify PIN dan Karyawan
            $karyawan = Karyawan::where('id_karyawan', $request->id_karyawan_pelapor)
                ->where('pin', $request->pin)
                ->where('status_aktif', 'Aktif')
                ->first();

            if (!$karyawan) {
                return redirect()->back()
                    ->withErrors(['error' => 'PIN tidak valid atau karyawan tidak aktif'])
                    ->withInput();
            }

            // Validasi sumber
            $sumber = $this->getSumberData($request->tipe_retur, $request->id_sumber);
            
            if (!$sumber) {
                return redirect()->back()
                    ->withErrors(['error' => 'Sumber data tidak ditemukan'])
                    ->withInput();
            }

            // Get kode referensi
            $kodeReferensi = $this->getKodeReferensi($request->tipe_retur, $sumber);

            // Create retur
            $retur = Retur::create([
                'tipe_retur' => $request->tipe_retur,
                'id_sumber' => $request->id_sumber,
                'kode_referensi' => $kodeReferensi,
                'status' => 'draft',
                'alasan_retur' => $request->alasan_retur,
                'keterangan_alasan' => $request->keterangan_alasan,
                'id_karyawan_pelapor' => $karyawan->id_karyawan,
                'unit_pelapor' => $request->unit_pelapor,
                'tanggal_retur' => $request->tanggal_retur,
                'unit_tujuan' => $request->unit_tujuan,
                'id_supplier' => $request->id_supplier,
                'catatan' => $request->catatan,
            ]);

            // Create retur items
            foreach ($request->items as $itemData) {
                $returItem = ReturItem::create([
                    'id_retur' => $retur->id_retur,
                    'id_item_sumber' => $itemData['id_item_sumber'],
                    'id_produk' => $itemData['id_produk'],
                    'nama_produk' => $itemData['nama_produk'],
                    'qty_diretur' => $itemData['qty_diretur'],
                    'harga_satuan' => $itemData['harga_satuan'],
                    'kondisi_barang' => $itemData['kondisi_barang'],
                    'catatan_item' => $itemData['catatan_item'] ?? null,
                ]);

                // Create batches if provided
                if (isset($itemData['batches']) && is_array($itemData['batches'])) {
                    foreach ($itemData['batches'] as $batchData) {
                        ReturItemBatch::create([
                            'id_retur_item' => $returItem->id_retur_item,
                            'batch_number' => $batchData['batch_number'],
                            'tanggal_kadaluarsa' => $batchData['tanggal_kadaluarsa'] ?? null,
                            'qty_diretur' => $batchData['qty_diretur'],
                            'kondisi' => $batchData['kondisi'] ?? $itemData['kondisi_barang'],
                            'catatan' => $batchData['catatan'] ?? null,
                        ]);
                    }
                }
            }

            // Create history
            ReturHistory::create([
                'id_retur' => $retur->id_retur,
                'status_dari' => 'draft',
                'status_ke' => 'draft',
                'id_karyawan' => $karyawan->id_karyawan,
                'catatan' => 'Retur dibuat oleh ' . $karyawan->nama_lengkap,
            ]);

            DB::commit();

            Alert::success('Berhasil', 'Retur berhasil dibuat');
            return redirect()->route('returs.show', $retur->id_retur);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating retur: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Gagal membuat retur: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified retur
     */
    public function show($id)
    {
        $retur = Retur::with([
            'returItems.batches',
            'returItems.produk',
            'karyawanPelapor',
            'karyawanApproval',
            'karyawanPemroses',
            'supplier',
            'histories.karyawan',
            'documents.karyawanUpload'
        ])->findOrFail($id);

        return view('returs.show', compact('retur'));
    }

    /**
     * Show the form for editing the specified retur
     */
    public function edit($id)
    {
        $retur = Retur::with(['returItems', 'supplier'])->findOrFail($id);

        if (!$retur->canEdit()) {
            return redirect()->route('returs.show', $retur->id_retur)
                ->withErrors(['error' => 'Retur tidak dapat diedit pada status ini']);
        }

        $suppliers = Supplier::where('status', 'aktif')->get();
        return view('returs.edit', compact('retur', 'suppliers'));
    }

    /**
     * Update the specified retur
     */
    public function update(Request $request, $id)
    {
        $retur = Retur::findOrFail($id);

        if (!$retur->canEdit()) {
            return redirect()->route('returs.show', $retur->id_retur)
                ->withErrors(['error' => 'Retur tidak dapat diedit pada status ini']);
        }

        $request->validate([
            'alasan_retur' => 'sometimes|in:barang_rusak,barang_kadaluarsa,barang_tidak_sesuai,kelebihan_pengiriman,kesalahan_order,kualitas_tidak_baik,lainnya',
            'keterangan_alasan' => 'nullable|string',
            'tanggal_retur' => 'sometimes|date',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $retur->status;

            $retur->update($request->only([
                'alasan_retur',
                'keterangan_alasan',
                'tanggal_retur',
                'unit_tujuan',
                'id_supplier',
                'catatan'
            ]));

            // Create history if needed
            if ($request->has('status') && $request->status !== $oldStatus) {
                ReturHistory::create([
                    'id_retur' => $retur->id_retur,
                    'status_dari' => $oldStatus,
                    'status_ke' => $request->status,
                    'id_karyawan' => Auth::id(),
                    'catatan' => $request->catatan_history ?? 'Retur diupdate',
                ]);
            }

            DB::commit();

            Alert::success('Berhasil', 'Retur berhasil diupdate');
            return redirect()->route('returs.show', $retur->id_retur);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal update retur: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Submit retur untuk persetujuan
     * Supports both AJAX (JSON) and regular form submission
     */
    public function submit(Request $request, $id)
    {
        // Force JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            try {
                $retur = Retur::findOrFail($id);

                if ($retur->status !== 'draft') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Retur hanya dapat disubmit dari status draft'
                    ], 422);
                }

                // Validate PIN
                $request->validate([
                    'pin' => 'required|string|size:6',
                    'id_karyawan' => 'required|uuid',
                ]);

                // Verify PIN
                $karyawan = Karyawan::where('id_karyawan', $request->id_karyawan)
                    ->where('pin', $request->pin)
                    ->where('status_aktif', 'Aktif')
                    ->first();

                if (!$karyawan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PIN tidak valid atau karyawan tidak aktif'
                    ], 422);
                }

                DB::beginTransaction();
                
                $oldStatus = $retur->status;
                $newStatus = 'menunggu_persetujuan';

                $retur->update([
                    'status' => $newStatus
                ]);

                // Create history
                ReturHistory::create([
                    'id_retur' => $retur->id_retur,
                    'status_dari' => $oldStatus,
                    'status_ke' => $newStatus,
                    'id_karyawan' => $karyawan->id_karyawan,
                    'catatan' => 'Retur diajukan untuk persetujuan oleh ' . $karyawan->nama_lengkap,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Retur berhasil disubmit untuk persetujuan'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error submitting retur: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal submit retur: ' . $e->getMessage()
                ], 500);
            }
        }

        // Regular form submission (non-AJAX)
        $retur = Retur::findOrFail($id);

        if ($retur->status !== 'draft') {
            return redirect()->back()
                ->withErrors(['error' => 'Retur hanya dapat disubmit dari status draft']);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $retur->status;
            $newStatus = 'menunggu_persetujuan';

            $retur->update([
                'status' => $newStatus
            ]);

            // Create history
            $karyawanId = Auth::id();
            $karyawan = Karyawan::find($karyawanId);
            
            ReturHistory::create([
                'id_retur' => $retur->id_retur,
                'status_dari' => $oldStatus,
                'status_ke' => $newStatus,
                'id_karyawan' => $karyawanId,
                'catatan' => 'Retur diajukan untuk persetujuan oleh ' . ($karyawan->nama_lengkap ?? 'System'),
            ]);

            DB::commit();

            return redirect()->route('returs.show', $retur->id_retur)
                ->with('success', 'Retur berhasil disubmit untuk persetujuan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting retur: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal submit retur: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve atau reject retur
     */
    public function approve(Request $request, $id)
    {
        // Force JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            try {
                $retur = Retur::findOrFail($id);

                if ($retur->status !== 'menunggu_persetujuan') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Retur hanya dapat diapprove dari status menunggu persetujuan'
                    ], 422);
                }

                // Validate
                $request->validate([
                    'action' => 'required|in:approve,reject',
                    'pin' => 'required|string|size:6',
                    'id_karyawan' => 'required|uuid',
                    'catatan' => 'nullable|string',
                ]);

                // Verify PIN
                $karyawan = Karyawan::where('id_karyawan', $request->id_karyawan)
                    ->where('pin', $request->pin)
                    ->where('status_aktif', 'Aktif')
                    ->first();

                if (!$karyawan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PIN tidak valid atau karyawan tidak aktif'
                    ], 422);
                }

                DB::beginTransaction();
                
                $oldStatus = $retur->status;
                $newStatus = $request->action === 'approve' ? 'disetujui' : 'ditolak';
                $statusApproval = $request->action === 'approve' ? 'disetujui' : 'ditolak';

                $retur->update([
                    'status' => $newStatus,
                    'status_approval' => $statusApproval,
                    'id_karyawan_approval' => $karyawan->id_karyawan,
                    'tanggal_approval' => now(),
                    'catatan_approval' => $request->catatan,
                ]);

                // Create history
                ReturHistory::create([
                    'id_retur' => $retur->id_retur,
                    'status_dari' => $oldStatus,
                    'status_ke' => $newStatus,
                    'id_karyawan' => $karyawan->id_karyawan,
                    'catatan' => $request->action === 'approve' 
                        ? 'Retur disetujui oleh ' . $karyawan->nama_lengkap . ($request->catatan ? ': ' . $request->catatan : '')
                        : 'Retur ditolak oleh ' . $karyawan->nama_lengkap . ': ' . ($request->catatan ?? ''),
                ]);

                DB::commit();

                $message = $request->action === 'approve' 
                    ? 'Retur berhasil disetujui' 
                    : 'Retur ditolak';

                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing approval: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses approval: ' . $e->getMessage()
                ], 500);
            }
        }

        // Regular form submission (non-AJAX)
        $retur = Retur::findOrFail($id);

        if ($retur->status !== 'menunggu_persetujuan') {
            return redirect()->back()
                ->withErrors(['error' => 'Retur hanya dapat diapprove dari status menunggu persetujuan']);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $retur->status;
            $newStatus = $request->action === 'approve' ? 'disetujui' : 'ditolak';
            $statusApproval = $request->action === 'approve' ? 'disetujui' : 'ditolak';

            $retur->update([
                'status' => $newStatus,
                'status_approval' => $statusApproval,
                'id_karyawan_approval' => Auth::id(),
                'tanggal_approval' => now(),
                'catatan_approval' => $request->catatan,
            ]);

            // Create history
            ReturHistory::create([
                'id_retur' => $retur->id_retur,
                'status_dari' => $oldStatus,
                'status_ke' => $newStatus,
                'id_karyawan' => Auth::id(),
                'catatan' => $request->action === 'approve' 
                    ? 'Retur disetujui' 
                    : 'Retur ditolak: ' . ($request->catatan ?? ''),
            ]);

            DB::commit();

            $message = $request->action === 'approve' 
                ? 'Retur berhasil disetujui' 
                : 'Retur ditolak';

            return redirect()->route('returs.show', $retur->id_retur)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing approval: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal memproses approval: ' . $e->getMessage()]);
        }
    }

    /**
     * Proses retur (mulai proses retur)
     * Supports both AJAX (JSON) and regular form submission
     */
   public function process(Request $request, $id)
    {
        // Force JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            try {
                $retur = Retur::with('returItems.batches')->findOrFail($id);

                if (!$retur->canProcess()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Retur tidak dapat diproses pada status ini'
                    ], 422);
                }

                // Validate PIN
                $request->validate([
                    'pin' => 'required|string|size:6',
                    'id_karyawan' => 'required|uuid',
                ]);

                // Verify PIN
                $karyawan = Karyawan::where('id_karyawan', $request->id_karyawan)
                    ->where('pin', $request->pin)
                    ->where('status_aktif', 'Aktif')
                    ->first();

                if (!$karyawan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PIN tidak valid atau karyawan tidak aktif'
                    ], 422);
                }

                DB::beginTransaction();
                
                $oldStatus = $retur->status;
                $newStatus = 'diproses';

                // Kurangi stock berdasarkan tipe retur SEBELUM update status
                $this->reduceStock($retur);

                $retur->update([
                    'status' => $newStatus,
                    'id_karyawan_pemroses' => $karyawan->id_karyawan,
                    'tanggal_diproses' => now(),
                ]);

                // Create history
                ReturHistory::create([
                    'id_retur' => $retur->id_retur,
                    'status_dari' => $oldStatus,
                    'status_ke' => $newStatus,
                    'id_karyawan' => $karyawan->id_karyawan,
                    'catatan' => 'Retur mulai diproses oleh ' . $karyawan->nama_lengkap . '. Stock telah dikurangi.',
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Retur berhasil diproses dan stock telah dikurangi'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error processing retur: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses retur: ' . $e->getMessage()
                ], 500);
            }
        }

        // Regular form submission (non-AJAX)
        $retur = Retur::with('returItems.batches')->findOrFail($id);

        if (!$retur->canProcess()) {
            return redirect()->back()
                ->withErrors(['error' => 'Retur tidak dapat diproses pada status ini']);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $retur->status;
            $newStatus = 'diproses';

            $karyawanId = Auth::id();

            // Kurangi stock berdasarkan tipe retur SEBELUM update status
            $this->reduceStock($retur);

            $retur->update([
                'status' => $newStatus,
                'id_karyawan_pemroses' => $karyawanId,
                'tanggal_diproses' => now(),
            ]);

            // Create history
            $karyawan = Karyawan::find($karyawanId);
            
            ReturHistory::create([
                'id_retur' => $retur->id_retur,
                'status_dari' => $oldStatus,
                'status_ke' => $newStatus,
                'id_karyawan' => $karyawanId,
                'catatan' => 'Retur mulai diproses oleh ' . ($karyawan->nama_lengkap ?? 'System') . '. Stock telah dikurangi.',
            ]);

            DB::commit();

            return redirect()->route('returs.show', $retur->id_retur)
                ->with('success', 'Retur berhasil diproses dan stock telah dikurangi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing retur: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal memproses retur: ' . $e->getMessage()]);
        }
    }

    /**
     * Reduce stock based on retur type
     * 
     * @param Retur $retur
     * @throws \Exception
     */
    private function reduceStock($retur)
    {
        try {
            if ($retur->tipe_retur === 'po') {
                $this->reduceStockFromPO($retur);
            } elseif ($retur->tipe_retur === 'stock_apotik') {
                $this->reduceStockFromApotik($retur);
            }
        } catch (\Exception $e) {
            Log::error('Error reducing stock: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reduce stock from Gudang (for PO returns)
     * 
     * @param Retur $retur
     * @throws \Exception
     */
    private function reduceStockFromPO($retur)
    {
        foreach ($retur->returItems as $returItem) {
            // Jika ada batches, kurangi per batch
            if ($returItem->batches && $returItem->batches->isNotEmpty()) {
                foreach ($returItem->batches as $batch) {
                    $detailGudang = DetailGudang::where('barang_id', $returItem->id_produk)
                        ->where('no_batch', $batch->batch_number)
                        ->first();

                    if (!$detailGudang) {
                        throw new \Exception("Stock gudang tidak ditemukan untuk produk {$returItem->nama_produk} batch {$batch->batch_number}");
                    }

                    if ($detailGudang->stock_gudang < $batch->qty_diretur) {
                        throw new \Exception("Stock gudang tidak mencukupi untuk produk {$returItem->nama_produk} batch {$batch->batch_number}. Stock tersedia: {$detailGudang->stock_gudang}, diminta: {$batch->qty_diretur}");
                    }

                    // Kurangi stock
                    $detailGudang->decrement('stock_gudang', $batch->qty_diretur);
                    
                    Log::info("Stock gudang dikurangi: {$returItem->nama_produk} batch {$batch->batch_number} sebanyak {$batch->qty_diretur}");
                }
            } else {
                // Jika tidak ada batches, kurangi dari total stock produk
                $detailGudang = DetailGudang::where('barang_id', $returItem->id_produk)
                    ->orderBy('tanggal_kadaluarsa', 'asc') // FIFO
                    ->first();

                if (!$detailGudang) {
                    throw new \Exception("Stock gudang tidak ditemukan untuk produk {$returItem->nama_produk}");
                }

                if ($detailGudang->stock_gudang < $returItem->qty_diretur) {
                    throw new \Exception("Stock gudang tidak mencukupi untuk produk {$returItem->nama_produk}. Stock tersedia: {$detailGudang->stock_gudang}, diminta: {$returItem->qty_diretur}");
                }

                // Kurangi stock
                $detailGudang->decrement('stock_gudang', $returItem->qty_diretur);
                
                Log::info("Stock gudang dikurangi: {$returItem->nama_produk} sebanyak {$returItem->qty_diretur}");
            }
        }
    }

    /**
     * Reduce stock from Apotik (for stock_apotik returns)
     * 
     * @param Retur $retur
     * @throws \Exception
     */
    private function reduceStockFromApotik($retur)
    {
        foreach ($retur->returItems as $returItem) {
            // Jika ada batches, kurangi per batch
            if ($returItem->batches && $returItem->batches->isNotEmpty()) {
                foreach ($returItem->batches as $batch) {
                    $detailStockApotik = DetailStockApotik::where('detail_obat_rs_id', $returItem->id_produk)
                        ->where('no_batch', $batch->batch_number)
                        ->first();

                    if (!$detailStockApotik) {
                        throw new \Exception("Stock apotik tidak ditemukan untuk produk {$returItem->nama_produk} batch {$batch->batch_number}");
                    }

                    if ($detailStockApotik->stock_apotik < $batch->qty_diretur) {
                        throw new \Exception("Stock apotik tidak mencukupi untuk produk {$returItem->nama_produk} batch {$batch->batch_number}. Stock tersedia: {$detailStockApotik->stock_apotik}, diminta: {$batch->qty_diretur}");
                    }

                    // Kurangi stock
                    $detailStockApotik->decrement('stock_apotik', $batch->qty_diretur);
                    
                    Log::info("Stock apotik dikurangi: {$returItem->nama_produk} batch {$batch->batch_number} sebanyak {$batch->qty_diretur}");
                }
            } else {
                // Jika tidak ada batches, kurangi dari total stock produk
                $detailStockApotik = DetailstockApotik::where('detail_obat_rs_id', $returItem->id_produk)
                    ->orderBy('tanggal_kadaluarsa', 'asc') // FIFO
                    ->first();

                if (!$detailStockApotik) {
                    throw new \Exception("Stock apotik tidak ditemukan untuk produk {$returItem->nama_produk}");
                }

                if ($detailStockApotik->stock_apotik < $returItem->qty_diretur) {
                    throw new \Exception("Stock apotik tidak mencukupi untuk produk {$returItem->nama_produk}. Stock tersedia: {$detailStockApotik->stock_apotik}, diminta: {$returItem->qty_diretur}");
                }

                // Kurangi stock
                $detailStockApotik->decrement('stock_apotik', $returItem->qty_diretur);
                
                Log::info("Stock apotik dikurangi: {$returItem->nama_produk} sebanyak {$returItem->qty_diretur}");
            }
        }
    }

    /**
     * Complete retur (selesaikan retur)
     * Supports both AJAX (JSON) and regular form submission
     */
    public function complete(Request $request, $id)
    {
        // Force JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            try {
                $retur = Retur::findOrFail($id);

                if ($retur->status !== 'diproses') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Retur hanya dapat diselesaikan dari status diproses'
                    ], 422);
                }

                // Validate PIN
                $request->validate([
                    'pin' => 'required|string|size:6',
                    'id_karyawan' => 'required|uuid',
                ]);

                // Verify PIN
                $karyawan = Karyawan::where('id_karyawan', $request->id_karyawan)
                    ->where('pin', $request->pin)
                    ->where('status_aktif', 'Aktif')
                    ->first();

                if (!$karyawan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PIN tidak valid atau karyawan tidak aktif'
                    ], 422);
                }

                DB::beginTransaction();
                
                $oldStatus = $retur->status;
                $newStatus = 'selesai';

                $retur->update([
                    'status' => $newStatus,
                    'tanggal_selesai' => now(),
                ]);

                // Create history
                ReturHistory::create([
                    'id_retur' => $retur->id_retur,
                    'status_dari' => $oldStatus,
                    'status_ke' => $newStatus,
                    'id_karyawan' => $karyawan->id_karyawan,
                    'catatan' => 'Retur selesai diproses oleh ' . $karyawan->nama_lengkap,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Retur berhasil diselesaikan'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error completing retur: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyelesaikan retur: ' . $e->getMessage()
                ], 500);
            }
        }

        // Regular form submission (non-AJAX)
        $retur = Retur::findOrFail($id);

        if ($retur->status !== 'diproses') {
            return redirect()->back()
                ->withErrors(['error' => 'Retur hanya dapat diselesaikan dari status diproses']);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $retur->status;
            $newStatus = 'selesai';

            $retur->update([
                'status' => $newStatus,
                'tanggal_selesai' => now(),
            ]);

            // Create history
            $karyawanId = Auth::id();
            $karyawan = Karyawan::find($karyawanId);
            
            ReturHistory::create([
                'id_retur' => $retur->id_retur,
                'status_dari' => $oldStatus,
                'status_ke' => $newStatus,
                'id_karyawan' => $karyawanId,
                'catatan' => 'Retur selesai diproses oleh ' . ($karyawan->nama_lengkap ?? 'System'),
            ]);

            DB::commit();

            return redirect()->route('returs.show', $retur->id_retur)
                ->with('success', 'Retur berhasil diselesaikan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing retur: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menyelesaikan retur: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel retur
     * Supports both AJAX (JSON) and regular form submission
     */
    public function cancel(Request $request, $id)
    {
        // Force JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            try {
                $retur = Retur::findOrFail($id);

                if (in_array($retur->status, ['selesai', 'dibatalkan'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Retur tidak dapat dibatalkan pada status ini'
                    ], 422);
                }

                // Validate
                $request->validate([
                    'alasan_pembatalan' => 'required|string',
                    'pin' => 'required|string|size:6',
                    'id_karyawan' => 'required|uuid',
                ]);

                // Verify PIN
                $karyawan = Karyawan::where('id_karyawan', $request->id_karyawan)
                    ->where('pin', $request->pin)
                    ->where('status_aktif', 'Aktif')
                    ->first();

                if (!$karyawan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'PIN tidak valid atau karyawan tidak aktif'
                    ], 422);
                }

                DB::beginTransaction();
                
                $oldStatus = $retur->status;
                $newStatus = 'dibatalkan';

                $retur->update([
                    'status' => $newStatus,
                ]);

                // Create history
                ReturHistory::create([
                    'id_retur' => $retur->id_retur,
                    'status_dari' => $oldStatus,
                    'status_ke' => $newStatus,
                    'id_karyawan' => $karyawan->id_karyawan,
                    'catatan' => 'Retur dibatalkan oleh ' . $karyawan->nama_lengkap . ': ' . $request->alasan_pembatalan,
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Retur berhasil dibatalkan'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Error cancelling retur: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membatalkan retur: ' . $e->getMessage()
                ], 500);
            }
        }

        // Regular form submission (non-AJAX)
        $retur = Retur::findOrFail($id);

        if (in_array($retur->status, ['selesai', 'dibatalkan'])) {
            return redirect()->back()
                ->withErrors(['error' => 'Retur tidak dapat dibatalkan pada status ini']);
        }

        $request->validate([
            'alasan_pembatalan' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $retur->status;
            $newStatus = 'dibatalkan';

            $retur->update([
                'status' => $newStatus,
            ]);

            // Create history
            $karyawanId = Auth::id();
            $karyawan = Karyawan::find($karyawanId);
            
            ReturHistory::create([
                'id_retur' => $retur->id_retur,
                'status_dari' => $oldStatus,
                'status_ke' => $newStatus,
                'id_karyawan' => $karyawanId,
                'catatan' => 'Retur dibatalkan oleh ' . ($karyawan->nama_lengkap ?? 'System') . ': ' . $request->alasan_pembatalan,
            ]);

            DB::commit();

            return redirect()->route('returs.show', $retur->id_retur)
                ->with('success', 'Retur berhasil dibatalkan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling retur: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Gagal membatalkan retur: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete retur (soft delete)
     */
    public function destroy($id)
    {
        $retur = Retur::findOrFail($id);

        if (!in_array($retur->status, ['draft', 'ditolak', 'dibatalkan'])) {
            return redirect()->back()
                ->withErrors(['error' => 'Retur hanya dapat dihapus jika berstatus draft, ditolak, atau dibatalkan']);
        }

        DB::beginTransaction();
        try {
            $retur->delete();

            DB::commit();

            return redirect()->route('returs.index')
                ->with('success', 'Retur berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus retur: ' . $e->getMessage()]);
        }
    }

    /**
     * Get available items untuk retur dari PO atau Stock Apotik (untuk AJAX)
     */
    public function getAvailableItems(Request $request)
    {
        $request->validate([
            'tipe_retur' => 'required|in:po,stock_apotik',
            'id_sumber' => 'required|uuid',
        ]);

        try {
            if ($request->tipe_retur === 'po') {
                $items = $this->getAvailableItemsFromPO($request->id_sumber);
            } else {
                $items = $this->getAvailableItemsFromStockApotik($request->id_sumber);
            }

            return response()->json([
                'success' => true,
                'data' => $items
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting available items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data items: ' . $e->getMessage()
            ], 500);
        }
    }

    // ============ Helper Methods ============

    /**
     * Get sumber data (PO atau Stock Apotik)
     */
    private function getSumberData($tipe, $id)
    {
        if ($tipe === 'po') {
            return PurchaseOrder::find($id);
        } elseif ($tipe === 'stock_apotik') {
            return StockApotik::find($id);
        }
        return null;
    }

    /**
     * Get kode referensi dari sumber
     */
    private function getKodeReferensi($tipe, $sumber)
    {
        if ($tipe === 'po') {
            return $sumber->no_po;
        } elseif ($tipe === 'stock_apotik') {
            return $sumber->kode_transaksi;
        }
        return null;
    }

    /**
     * Get available items from Purchase Order
     */
    private function getAvailableItemsFromPO($idPo)
    {
        $po = PurchaseOrder::with(['items.produk', 'items.batches'])
            ->findOrFail($idPo);

        $items = $po->items->map(function ($item) {
            return [
                'id_po_item' => $item->id_po_item,
                'id_produk' => $item->id_produk,
                'nama_produk' => $item->nama_produk,
                'qty_diterima' => $item->qty_diterima,
                'harga_satuan' => $item->produk->harga_beli,
                'batches' => $item->batches->map(function ($batch) {
                    return [
                        'batch_number' => $batch->batch_number,
                        'tanggal_kadaluarsa' => $batch->tanggal_kadaluarsa,
                        'qty_diterima' => $batch->qty_diterima,
                        'kondisi' => $batch->kondisi,
                    ];
                }),
            ];
        });

        return $items;
    }

    /**
     * Get available items from Stock Apotik
     */
    private function getAvailableItemsFromStockApotik($idStockApotik)
    {
        $stockApotik = StockApotik::with(['details.detailObatRs', 'details.detailSupplier'])
            ->findOrFail($idStockApotik);

        $items = $stockApotik->details->map(function ($detail) {
            return [
                'id_detail_stock_apotik' => $detail->id,
                'id_produk' => $detail->detail_obat_rs_id,
                'nama_produk' => $detail->detailObatRs->nama_obat_rs ?? 'N/A',
                'batch_number' => $detail->no_batch,
                'no_batch' => $detail->no_batch,
                'tanggal_kadaluarsa' => $detail->tanggal_kadaluarsa,
                'stock_apotik' => $detail->stock_apotik,
                'harga_satuan' => $detail->detailSupplier->harga_beli ?? 0,
            ];
        });

        return $items;
    }
}