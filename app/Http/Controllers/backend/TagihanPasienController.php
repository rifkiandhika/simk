<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Pasien;
use App\Models\Registrasi;
use App\Models\Tagihan;
use App\Models\TagihanItem;
use App\Services\TagihanPembayaranService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanPasienController extends Controller
{
    protected $pembayaranService;

    public function __construct(TagihanPembayaranService $pembayaranService)
    {
        $this->pembayaranService = $pembayaranService;
    }

    /**
     * Display listing of tagihan
     */
    public function index(Request $request)
    {
        $query = Tagihan::with(['pasien', 'registrasi']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('no_tagihan', 'LIKE', "%{$search}%")
                    ->orWhereHas('pasien', function ($q2) use ($search) {
                        $q2->where('nama', 'LIKE', "%{$search}%")
                            ->orWhere('no_rm', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Jenis filter
        if ($request->filled('jenis')) {
            $query->where('jenis_tagihan', $request->jenis);
        }

        // Date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_tagihan', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_tagihan', '<=', $request->end_date);
        }

        // Order by latest
        $query->orderBy('tanggal_tagihan', 'DESC');

        $tagihans = $query->paginate(20);

        // Summary statistics
        $summary = [
            'belum_lunas' => Tagihan::where('status', 'BELUM_LUNAS')->count(),
            'cicilan' => Tagihan::where('status', 'CICILAN')->count(),
            'lunas' => Tagihan::where('status', 'LUNAS')->count(),
            'total_piutang' => Tagihan::whereIn('status', ['BELUM_LUNAS', 'CICILAN'])
                ->sum('sisa_tagihan'),
        ];

        return view('tagihans.index', compact('tagihans', 'summary'));
    }

    /**
     * Show detail tagihan
     */
    public function show($id)
    {
        $tagihan = Tagihan::with([
            'pasien',
            'registrasi',
            'items',
            'pembayarans.creator',
            'creator'
        ])->findOrFail($id);

        return view('tagihans.show', compact('tagihan'));
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        // Get registrasi if provided
        $registrasi = null;
        if ($request->filled('id_registrasi')) {
            $registrasi = Registrasi::with('pasien')->findOrFail($request->id_registrasi);
        }

        $pasiens = Pasien::orderBy('nama_lengkap')->get();

        return view('tagihans.create', compact('pasiens', 'registrasi'));
    }

    /**
     * Store new tagihan
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_registrasi' => 'required|exists:registrasis,id_registrasi',
            'id_pasien' => 'required|exists:pasiens,id_pasien',
            'tanggal_tagihan' => 'required|date',
            'jenis_tagihan' => 'required|in:IGD,RAWAT_JALAN,RAWAT_INAP',
            'status_klaim' => 'nullable|in:NON_KLAIM,PENDING,DISETUJUI,DITOLAK',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.kategori' => 'required|in:APOTIK,TINDAKAN,LAB,RADIOLOGI,KAMAR,ADMIN',
            'items.*.deskripsi' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.ditanggung' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create tagihan
            $tagihan = Tagihan::create([
                'id_registrasi' => $validated['id_registrasi'],
                'id_pasien' => $validated['id_pasien'],
                'tanggal_tagihan' => $validated['tanggal_tagihan'],
                'jenis_tagihan' => $validated['jenis_tagihan'],
                'status_klaim' => $validated['status_klaim'] ?? 'NON_KLAIM',
                'catatan' => $validated['catatan'] ?? null,
                'created_by' => auth()->user()->id_karyawan,
            ]);

            // Create items
            foreach ($validated['items'] as $itemData) {
                TagihanItem::create([
                    'id_tagihan' => $tagihan->id_tagihan,
                    'kategori' => $itemData['kategori'],
                    'deskripsi' => $itemData['deskripsi'],
                    'qty' => $itemData['qty'],
                    'harga' => $itemData['harga'],
                    'ditanggung' => $itemData['ditanggung'] ?? false,
                    'created_by' => auth()->user()->id_karyawan,
                ]);
            }

            DB::commit();

            return redirect()->route('tagihans.show', $tagihan->id_tagihan)
                ->with('success', 'Tagihan berhasil dibuat');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $tagihan = Tagihan::with(['items', 'pasien', 'registrasi'])
            ->findOrFail($id);

        if ($tagihan->locked) {
            return back()->with('error', 'Tagihan sudah dikunci, tidak dapat diubah');
        }

        $pasiens = Pasien::orderBy('nama_lengkap')->get();

        return view('tagihans.edit', compact('tagihan', 'pasiens'));
    }

    /**
     * Update tagihan
     */
    public function update(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);

        if ($tagihan->locked) {
            return back()->with('error', 'Tagihan sudah dikunci, tidak dapat diubah');
        }

        if ($tagihan->total_dibayar > 0) {
            return back()->with('error', 'Tagihan sudah ada pembayaran, tidak dapat diubah');
        }

        $validated = $request->validate([
            'tanggal_tagihan' => 'required|date',
            'jenis_tagihan' => 'required|in:IGD,RAWAT_JALAN,RAWAT_INAP',
            'status_klaim' => 'nullable|in:NON_KLAIM,PENDING,DISETUJUI,DITOLAK',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.kategori' => 'required|in:APOTIK,TINDAKAN,LAB,RADIOLOGI,KAMAR,ADMIN',
            'items.*.deskripsi' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.harga' => 'required|numeric|min:0',
            'items.*.ditanggung' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Update tagihan
            $tagihan->update([
                'tanggal_tagihan' => $validated['tanggal_tagihan'],
                'jenis_tagihan' => $validated['jenis_tagihan'],
                'status_klaim' => $validated['status_klaim'] ?? 'NON_KLAIM',
                'catatan' => $validated['catatan'] ?? null,
            ]);

            // Delete old items
            $tagihan->items()->delete();

            // Create new items
            foreach ($validated['items'] as $itemData) {
                TagihanItem::create([
                    'id_tagihan' => $tagihan->id_tagihan,
                    'kategori' => $itemData['kategori'],
                    'deskripsi' => $itemData['deskripsi'],
                    'qty' => $itemData['qty'],
                    'harga' => $itemData['harga'],
                    'ditanggung' => $itemData['ditanggung'] ?? false,
                    'created_by' => auth()->user()->id_karyawan,
                ]);
            }

            DB::commit();

            return redirect()->route('tagihans.show', $tagihan->id_tagihan)
                ->with('success', 'Tagihan berhasil diupdate');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Gagal update tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Delete tagihan
     */
    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);

        if ($tagihan->locked) {
            return back()->with('error', 'Tagihan sudah dikunci, tidak dapat dihapus');
        }

        if ($tagihan->total_dibayar > 0) {
            return back()->with('error', 'Tagihan sudah ada pembayaran, tidak dapat dihapus');
        }

        try {
            $tagihan->delete();
            return redirect()->route('tagihans.index')
                ->with('success', 'Tagihan berhasil dihapus');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menghapus tagihan: ' . $e->getMessage());
        }
    }

    /**
     * Show payment form
     */
    public function payment($id)
    {
        $tagihan = Tagihan::with(['pasien', 'items', 'pembayarans'])
            ->findOrFail($id);

        if ($tagihan->locked) {
            return back()->with('error', 'Tagihan sudah dikunci, tidak dapat dibayar');
        }

        if ($tagihan->status == 'LUNAS') {
            return back()->with('error', 'Tagihan sudah lunas');
        }

        return view('tagihans.payment', compact('tagihan'));
    }

    /**
     * Store payment
     */
    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'id_tagihan' => 'required|exists:tagihans,id_tagihan',
            'tanggal_bayar' => 'nullable|date',
            'jumlah_bayar' => 'required|numeric|min:0.01',
            'metode' => 'required|in:TUNAI,DEBIT,CREDIT,TRANSFER,BPJS,ASURANSI,GIRO',
            'no_referensi' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string|max:500',
            'bukti_pembayaran' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'pin' => 'required|digits:6',
        ]);

        try {
            // Upload bukti (jika ada)
            if ($request->hasFile('bukti_pembayaran')) {
                $validated['bukti_pembayaran'] = $request->file('bukti_pembayaran')
                    ->store('pembayaran/bukti', 'public');
            }

            // 👉 SERAHKAN SEMUA KE SERVICE
            $result = $this->pembayaranService->prosesPembayaran($validated);

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses',
                'karyawan' => $result['karyawan'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Print tagihan
     */
    public function print($id)
    {
        $tagihan = Tagihan::with([
            'pasien',
            'items',
            'pembayarans',
            'creator'
        ])->findOrFail($id);

        return view('tagihans.print', compact('tagihan'));
    }

    /**
     * Export to Excel/PDF
     */
    public function export(Request $request, $format = 'excel')
    {
        // Implement export logic here
        // Use Laravel Excel or DomPDF
    }

    /**
     * Get tagihan summary/dashboard
     */
    public function dashboard()
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');

        $stats = [
            'today' => [
                'total' => Tagihan::whereDate('tanggal_tagihan', $today)->count(),
                'lunas' => Tagihan::whereDate('tanggal_tagihan', $today)
                    ->where('status', 'LUNAS')->count(),
                'revenue' => Tagihan::whereDate('tanggal_tagihan', $today)
                    ->sum('total_dibayar'),
            ],
            'month' => [
                'total' => Tagihan::where('tanggal_tagihan', 'LIKE', "{$thisMonth}%")->count(),
                'lunas' => Tagihan::where('tanggal_tagihan', 'LIKE', "{$thisMonth}%")
                    ->where('status', 'LUNAS')->count(),
                'revenue' => Tagihan::where('tanggal_tagihan', 'LIKE', "{$thisMonth}%")
                    ->sum('total_dibayar'),
            ],
            'piutang' => [
                'total' => Tagihan::whereIn('status', ['BELUM_LUNAS', 'CICILAN'])->count(),
                'amount' => Tagihan::whereIn('status', ['BELUM_LUNAS', 'CICILAN'])
                    ->sum('sisa_tagihan'),
            ],
        ];

        // Recent tagihan
        $recentTagihan = Tagihan::with(['pasien'])
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get();

        // Pending payments
        $pendingPayments = Tagihan::with(['pasien'])
            ->whereIn('status', ['BELUM_LUNAS', 'CICILAN'])
            ->orderBy('sisa_tagihan', 'DESC')
            ->limit(10)
            ->get();

        return view('tagihans.dashboard', compact('stats', 'recentTagihan', 'pendingPayments'));
    }
}
