<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DetailSupplier;
use App\Models\Jenis;
use App\Models\Satuan;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use RealRashid\SweetAlert\Facades\Alert;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::paginate(10);
        return view('supplier.index', compact('suppliers'));
    }

    public function create()
    {
        $departments = Department::paginate(10);
        $jenis = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuans = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();
        return view('supplier.create', compact('departments', 'jenis', 'satuans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'npwp'           => 'nullable|string|max:20|unique:suppliers,npwp',
            'nama_supplier'  => 'required|string|max:100',
            'alamat'         => 'nullable|string',
            'no_telp'        => 'nullable|string|max:15',
            'email'          => 'nullable|email|max:100',
            'kontak_person'  => 'nullable|string|max:100',
            'note'           => 'nullable|string',
            'file'           => 'nullable|mimes:pdf|max:2048',
            'file2'           => 'nullable|mimes:pdf|max:2048',
        ]);

        // === Handle upload file PDF ke public/uploads ===
        $filePath = null;
        if ($request->hasFile('file')) {
            $file      = $request->file('file');
            $fileName  = time() . '_' . $file->getClientOriginalName();
            $directory = public_path('uploads/supplier_files');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file->move($directory, $fileName);
            $filePath = 'uploads/supplier_files/' . $fileName;
        }
        $file2Path = null;
        if ($request->hasFile('file2')) {
            $file2      = $request->file('file2');
            $file2Name  = time() . '_' . $file2->getClientOriginalName();
            $directory  = public_path('uploads/supplier_files');

            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file2->move($directory, $file2Name);
            $file2Path = 'uploads/supplier_files/' . $file2Name;
        }

        // === Simpan data supplier ===
        $supplier = Supplier::create([
            'npwp'          => $request->npwp,
            'nama_supplier' => $request->nama_supplier,
            'alamat'        => $request->alamat,
            'no_telp'       => $request->no_telp,
            'email'         => $request->email,
            'kontak_person' => $request->kontak_person,
            'file'          => $filePath,
            'file2'         => $file2Path,
            'note'          => $request->note,
        ]);

        // === Simpan detail supplier jika ada ===
        if ($request->has('nama')) {
            foreach ($request->nama as $i => $nama) {
                if ($nama) {
                    $supplier->detailSuppliers()->create([
                        'department_id'  => $request->department_id[$i],
                        'no_batch'       => $request->no_batch[$i] ?? null,
                        'judul'          => $request->judul[$i] ?? '-',
                        'nama'           => $nama,
                        'jenis'          => $request->jenis[$i] ?? 'Lainnya',
                        'merk'           => $request->merk[$i] ?? null,
                        'satuan'         => $request->satuan[$i] ?? '-',
                        'exp_date'       => $request->exp_date[$i] ?? null,
                        'stock_live'     => $request->stock_live[$i] ?? 0,
                        'stock_po'       => $request->stock_po[$i] ?? 0,
                        'min_persediaan' => $request->min_persediaan[$i] ?? 0,
                        'harga_beli'     => $request->harga_beli[$i] ?? 0,
                        'kode_rak'       => $request->kode_rak[$i] ?? null,
                    ]);
                }
            }
        }

        Alert::success('Berhasil', 'Data supplier berhasil ditambahkan!');
        return redirect()->route('suppliers.index');
    }

    public function edit(Supplier $supplier)
    {

        $departments = Department::paginate(10);
        $supplier->load('detailSuppliers');
        $jenis = Jenis::where('status', 'Aktif')->orderBy('nama_jenis')->get();
        $satuans = Satuan::where('status', 'Aktif')->orderBy('nama_satuan')->get();
        return view('supplier.edit', compact('supplier', 'departments', 'jenis', 'satuans'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'npwp'           => 'nullable|string|max:20|unique:suppliers,npwp,' . $supplier->id,
            'nama_supplier'  => 'required|string|max:100',
            'alamat'         => 'nullable|string',
            'no_telp'        => 'nullable|string|max:15',
            'email'          => 'nullable|email|max:100',
            'kontak_person'  => 'nullable|string|max:100',
            'note'           => 'nullable|string',
            'file'           => 'nullable|mimes:pdf|max:2048',
            'file2'          => 'nullable|mimes:pdf|max:2048',
        ]);

        // === Handle file ===
        $filePath = $supplier->file;
        if ($request->hasFile('file')) {
            if ($supplier->file && File::exists(public_path($supplier->file))) {
                File::delete(public_path($supplier->file));
            }

            $file      = $request->file('file');
            $fileName  = time() . '_' . $file->getClientOriginalName();
            $directory = public_path('uploads/supplier_files');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file->move($directory, $fileName);
            $filePath = 'uploads/supplier_files/' . $fileName;
        }

        $file2Path = $supplier->file2;
        if ($request->hasFile('file2')) {
            if ($supplier->file2 && File::exists(public_path($supplier->file2))) {
                File::delete(public_path($supplier->file2));
            }

            $file2      = $request->file('file2');
            $file2Name  = time() . '_' . $file2->getClientOriginalName();
            $directory  = public_path('uploads/supplier_files');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0777, true, true);
            }

            $file2->move($directory, $file2Name);
            $file2Path = 'uploads/supplier_files/' . $file2Name;
        }

        // === Update data utama ===
        $supplier->update([
            'npwp'          => $request->npwp,
            'nama_supplier' => $request->nama_supplier,
            'alamat'        => $request->alamat,
            'no_telp'       => $request->no_telp,
            'email'         => $request->email,
            'kontak_person' => $request->kontak_person,
            'file'          => $filePath,
            'file2'         => $file2Path,
            'note'          => $request->note,
        ]);

        // === Update detail suppliers ===
        if ($request->has('nama')) {
            foreach ($request->nama as $i => $nama) {
                $detailId = $request->detail_id[$i] ?? null;

                $dataDetail = [
                    'department_id'  => $request->department_id[$i],
                    'no_batch'       => $request->no_batch[$i] ?? null,
                    'judul'          => $request->judul[$i] ?? '-',
                    'nama'           => $nama,
                    'jenis'          => $request->jenis[$i] ?? 'Lainnya',
                    'merk'           => $request->merk[$i] ?? null,
                    'satuan'         => $request->satuan[$i] ?? '-',
                    'exp_date'       => $request->exp_date[$i] ?? null,
                    'stock_live'     => $request->stock_live[$i] ?? 0,
                    'stock_po'       => $request->stock_po[$i] ?? 0,
                    'min_persediaan' => $request->min_persediaan[$i] ?? 0,
                    'harga_beli'     => $request->harga_beli[$i] ?? 0,
                    'kode_rak'       => $request->kode_rak[$i] ?? null,
                ];

                if ($detailId) {
                    $supplier->detailSuppliers()->where('id', $detailId)->update($dataDetail);
                } else {
                    $supplier->detailSuppliers()->create($dataDetail);
                }
            }
        }

        Alert::info('Berhasil', 'Data supplier berhasil diperbarui!');
        return redirect()->route('suppliers.index');
    }


    public function destroy(Supplier $supplier)
    {
        // hapus file PDF jika ada
        if ($supplier->file && file_exists(public_path($supplier->file))) {
            unlink(public_path($supplier->file));
        }

        $supplier->detailSuppliers()->delete();
        $supplier->delete();

        Alert::warning('Berhasil', 'Supplier berhasil dihapus!');
        return redirect()->route('suppliers.index');
    }
}
