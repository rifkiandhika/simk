<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Retur;
use App\Models\ReturDocument;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReturDocumentController extends Controller
{
    /**
     * Upload dokumen untuk retur
     */
    public function upload(Request $request, $idRetur)
    {
        // Force JSON response for AJAX requests
        if ($request->ajax() || $request->wantsJson() || $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            try {
                $retur = Retur::findOrFail($idRetur);

                // Validate
                $request->validate([
                    'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // Max 10MB
                    'tipe_dokumen' => 'required|in:foto_barang,surat_jalan,berita_acara,lainnya',
                    'keterangan' => 'nullable|string',
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

                $file = $request->file('file');
                $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('retur_documents/' . $idRetur, $fileName, 'public');

                $document = ReturDocument::create([
                    'id_retur' => $idRetur,
                    'nama_dokumen' => $file->getClientOriginalName(),
                    'file_path' => $filePath,
                    'file_type' => $file->getClientOriginalExtension(),
                    'tipe_dokumen' => $request->tipe_dokumen,
                    'keterangan' => $request->keterangan,
                    'id_karyawan_upload' => $karyawan->id_karyawan, // Menggunakan id_karyawan dari PIN verification
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dokumen berhasil diupload',
                    'data' => $document
                ], 201);

            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal: ' . $e->getMessage(),
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                \Log::error('Error uploading document: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal upload dokumen: ' . $e->getMessage()
                ], 500);
            }
        }

        // Regular form submission (non-AJAX) - Fallback
        $retur = Retur::findOrFail($idRetur);

        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240', // Max 10MB
            'tipe_dokumen' => 'required|in:foto_barang,surat_jalan,berita_acara,lainnya',
            'keterangan' => 'nullable|string',
        ]);

        try {
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('retur_documents/' . $idRetur, $fileName, 'public');

            $document = ReturDocument::create([
                'id_retur' => $idRetur,
                'nama_dokumen' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'file_type' => $file->getClientOriginalExtension(),
                'tipe_dokumen' => $request->tipe_dokumen,
                'keterangan' => $request->keterangan,
                'id_karyawan_upload' => Auth::id(),
            ]);

            return redirect()->back()->with('success', 'Dokumen berhasil diupload');

        } catch (\Exception $e) {
            \Log::error('Error uploading document: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal upload dokumen: ' . $e->getMessage()]);
        }
    }

    /**
     * Get all documents for a retur
     */
    public function index($idRetur)
    {
        $documents = ReturDocument::where('id_retur', $idRetur)
            ->with('karyawanUpload')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    /**
     * Delete dokumen
     */
    public function destroy($id)
    {
        $document = ReturDocument::findOrFail($id);

        try {
            // Delete file from storage
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Dokumen berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()
            ], 500);
        }
    }
}