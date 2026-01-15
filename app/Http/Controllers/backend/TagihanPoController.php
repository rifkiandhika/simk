<?php

namespace App\Http\Controllers\backend;

use App\Exports\TagihanExport;
use App\Http\Controllers\Controller;
use App\Models\TagihanPo;
use App\Models\PembayaranTagihan;
use App\Models\Karyawan;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class TagihanPoController extends Controller
{
    /**
     * List semua tagihan
     */
    public function index(Request $request)
    {
        // Get all suppliers for filter dropdown
        $suppliers = Supplier::orderBy('nama_supplier')->get();

        // Base query untuk tagihan aktif
        $queryAktif = TagihanPo::with(['purchaseOrder.supplier'])
            ->where('status', '!=', 'draft');

        // Base query untuk tagihan draft
        $queryDraft = TagihanPo::with(['purchaseOrder.supplier'])
            ->where('status', 'draft');

        // Apply filters based on active tab
        $tab = $request->get('tab', 'aktif');

        if ($tab === 'aktif') {
            $queryAktif = $this->applyFilters($queryAktif, $request);
        } else {
            $queryDraft = $this->applyFilters($queryDraft, $request);
        }

        // Handle Export Excel
        if ($request->has('export') && $request->export === 'excel') {
            return $this->exportExcel($request);
        }

        // Handle Export PDF
        if ($request->has('export') && $request->export === 'pdf') {
            return $this->exportPDF($request);
        }

        // Handle Print
        if ($request->has('print')) {
            return $this->printView($request);
        }

        // Get results - using get() instead of paginate
        $tagihanAktif = $queryAktif
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();

        $tagihanDraft = $queryDraft
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();


        return view('tagihan.index', compact(
            'tagihanAktif',
            'tagihanDraft',
            'suppliers'
        ));
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        // Filter by Supplier
        if ($request->filled('supplier_id')) {
            $query->whereHas('purchaseOrder', function ($q) use ($request) {
                $q->where('id_supplier', $request->supplier_id);
            });
        }

        // Filter by Status (only for aktif tab)
        if ($request->filled('status') && $request->get('tab') === 'aktif') {
            $query->where('status', $request->status);
        }

        // Filter by Jatuh Tempo (only for aktif tab)
        if ($request->filled('jatuh_tempo') && $request->get('tab') === 'aktif') {
            $today = now();

            if ($request->jatuh_tempo === 'lewat') {
                // Tagihan yang sudah lewat jatuh tempo
                $query->where('tanggal_jatuh_tempo', '<', $today)
                    ->whereNotIn('status', ['lunas', 'dibatalkan']);
            } elseif ($request->jatuh_tempo === 'minggu_ini') {
                // Tagihan yang jatuh tempo minggu ini
                $startOfWeek = $today->copy()->startOfWeek();
                $endOfWeek = $today->copy()->endOfWeek();

                $query->whereBetween('tanggal_jatuh_tempo', [$startOfWeek, $endOfWeek])
                    ->whereNotIn('status', ['lunas', 'dibatalkan']);
            }
        }

        // Filter by Date Range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_tagihan', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_tagihan', '<=', $request->tanggal_sampai);
        }

        // Filter by Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('no_tagihan', 'like', '%' . $search . '%')
                    ->orWhereHas('purchaseOrder', function ($subQ) use ($search) {
                        $subQ->where('no_gr', 'like', '%' . $search . '%')
                            ->orWhere('no_invoice', 'like', '%' . $search . '%')
                            ->orWhereHas('supplier', function ($supplierQ) use ($search) {
                                $supplierQ->where('nama_supplier', 'like', '%' . $search . '%');
                            });
                    });
            });
        }

        return $query;
    }

    /**
     * Export to Excel
     */
    private function exportExcel(Request $request)
    {
        $tab = $request->get('tab', 'aktif');

        // Build query based on tab
        if ($tab === 'aktif') {
            $query = TagihanPo::with(['purchaseOrder.supplier'])
                ->where('status', '!=', 'draft');
        } else {
            $query = TagihanPo::with(['purchaseOrder.supplier'])
                ->where('status', 'draft');
        }

        // Apply filters
        $query = $this->applyFilters($query, $request);
        $tagihan = $query->latest()->get();

        $filename = 'tagihan_' . $tab . '_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new TagihanExport($tagihan, $tab), $filename);
    }

    /**
     * Export to PDF
     */
    private function exportPDF(Request $request)
    {
        $tab = $request->get('tab', 'aktif');

        // Build query based on tab
        if ($tab === 'aktif') {
            $query = TagihanPo::with(['purchaseOrder.supplier'])
                ->where('status', '!=', 'draft');
        } else {
            $query = TagihanPo::with(['purchaseOrder.supplier'])
                ->where('status', 'draft');
        }

        // Apply filters
        $query = $this->applyFilters($query, $request);
        $tagihan = $query->latest()->get();

        $pdf = Pdf::loadView('tagihan.pdf', [
            'tagihan' => $tagihan,
            'tab' => $tab,
            'filters' => $request->all()
        ]);

        $filename = 'tagihan_' . $tab . '_' . date('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Print View
     */
    private function printView(Request $request)
    {
        $tab = $request->get('tab', 'aktif');

        // Build query based on tab
        if ($tab === 'aktif') {
            $query = TagihanPo::with(['purchaseOrder.supplier'])
                ->where('status', '!=', 'draft');
        } else {
            $query = TagihanPo::with(['purchaseOrder.supplier'])
                ->where('status', 'draft');
        }

        // Apply filters
        $query = $this->applyFilters($query, $request);
        $tagihan = $query->latest()->get();

        return view('tagihan.print', [
            'tagihan' => $tagihan,
            'tab' => $tab,
            'filters' => $request->all()
        ]);
    }

    /**
     * Detail tagihan
     */
    public function show($id_tagihan)
    {
        $tagihan = TagihanPo::with([
            'purchaseOrder.items',
            'items.produk',
            'supplier',
            'pembayaran.karyawanInput',
            'karyawanBuat',
        ])->findOrFail($id_tagihan);

        if (request()->wantsJson()) {
            return response()->json($tagihan, 200);
        }

        return view('tagihan.show', compact('tagihan'));
    }

    /**
     * Form input pembayaran
     */
    public function showPaymentForm($id_tagihan)
    {
        $tagihan = TagihanPo::with(['purchaseOrder', 'supplier', 'items'])
            ->findOrFail($id_tagihan);

        if (!$tagihan->canBePaid()) {
            return redirect()->route('tagihan.show', $id_tagihan)
                ->with('error', 'Tagihan ini tidak dapat dibayar');
        }

        return view('tagihan.payment-form', compact('tagihan'));
    }

    /**
     * Proses pembayaran
     */
    public function processPayment(Request $request, $id_tagihan)
    {
        $validator = Validator::make($request->all(), [
            'pin' => 'required|size:6',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tanggal_bayar' => 'required|date',
            'metode_pembayaran' => 'required|in:transfer,cash,giro,kartu_kredit,lainnya',
            'nomor_referensi' => 'nullable|string|max:100',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'catatan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // Verifikasi PIN
        $karyawan = Karyawan::where('id_karyawan', Auth::user()->id_karyawan)
            ->where('pin', $request->pin)
            ->first();

        if (!$karyawan) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'PIN tidak valid'], 403);
            }
            return back()->withErrors(['pin' => 'PIN tidak valid'])->withInput();
        }

        DB::beginTransaction();
        try {
            $tagihan = TagihanPo::findOrFail($id_tagihan);

            if (!$tagihan->canBePaid()) {
                throw new \Exception('Tagihan ini tidak dapat dibayar');
            }

            // Validasi jumlah bayar tidak melebihi sisa tagihan
            if ($request->jumlah_bayar > $tagihan->sisa_tagihan) {
                throw new \Exception('Jumlah pembayaran melebihi sisa tagihan');
            }

            // Upload bukti pembayaran
            $buktiBayar = null;
            if ($request->hasFile('bukti_pembayaran')) {
                $file = $request->file('bukti_pembayaran');
                $filename = 'bukti_' . time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('pembayaran', $filename, 'public');
                $buktiBayar = $path;
            }

            // Create pembayaran record dengan status langsung diverifikasi
            $pembayaran = PembayaranTagihan::create([
                'id_tagihan' => $tagihan->id_tagihan,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tanggal_bayar' => $request->tanggal_bayar,
                'metode_pembayaran' => $request->metode_pembayaran,
                'nomor_referensi' => $request->nomor_referensi,
                'bukti_pembayaran' => $buktiBayar,
                'catatan' => $request->catatan,
                'id_karyawan_input' => Auth::user()->id_karyawan,
                'status_pembayaran' => 'diverifikasi', // Langsung diverifikasi
                'id_karyawan_approve' => Auth::user()->id_karyawan, // Sama dengan yang input
                'tanggal_approve' => now(), // Langsung di-approve
            ]);

            // Update status tagihan langsung
            $tagihan->updatePembayaran();

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Pembayaran berhasil disimpan',
                    'data' => $pembayaran
                ], 201);
            }

            return redirect()->route('tagihan.show', $tagihan->id_tagihan)
                ->with('success', 'Pembayaran berhasil disimpan dan status tagihan telah diupdate');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * History pembayaran
     */
    public function paymentHistory($id_tagihan)
    {
        $tagihan = TagihanPo::with([
            'pembayaran.karyawanInput',
            'pembayaran.karyawanApprove'
        ])->findOrFail($id_tagihan);

        if (request()->wantsJson()) {
            return response()->json($tagihan->pembayaran, 200);
        }

        return view('tagihan.payment-history', compact('tagihan'));
    }

    /**
     * Download bukti pembayaran
     */
    public function downloadBukti($id_pembayaran)
    {
        $pembayaran = PembayaranTagihan::findOrFail($id_pembayaran);

        if (!$pembayaran->bukti_pembayaran) {
            abort(404, 'Bukti pembayaran tidak ditemukan');
        }

        $path = storage_path('app/public/' . $pembayaran->bukti_pembayaran);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($path);
    }

    /**
     * Print tagihan
     */
    public function print($id_tagihan)
    {
        $tagihan = TagihanPo::with([
            'purchaseOrder.items',
            'items.produk',
            'supplier',
            'pembayaran' => function ($q) {
                $q->where('status_pembayaran', 'diverifikasi');
            }
        ])->findOrFail($id_tagihan);

        return view('tagihan.print', compact('tagihan'));
    }
}
