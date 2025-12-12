<?php

namespace App\Http\Requests\alkes;

use Illuminate\Foundation\Http\FormRequest;

class AlkesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $alkesId = $this->route('alke');

        return [
            'kode_alkes' => [
                'required',
                'string',
                'max:50',
                'unique:alkes,kode_alkes,' . $alkesId . ',id'
            ],
            'nama_alkes' => 'required|string|max:200',
            'merk' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'spesifikasi' => 'nullable|string',
            'satuan' => 'required|string|max:50',
            'kategori' => 'required|in:Alat Medis,Alat Lab',
            'tanggal_kalibrasi_terakhir' => 'nullable|date',
            'tanggal_kalibrasi_berikutnya' => 'nullable|date|after_or_equal:tanggal_kalibrasi_terakhir',
            'maintenance_schedule' => 'nullable|string',
            'stok_minimal' => 'required|integer|min:0',
            'jumlah_stok' => 'required|integer|min:0',
            'no_batch' => 'nullable|string|max:50',
            'tanggal_kadaluarsa' => 'nullable|date',
            'kondisi' => 'required|in:Baik,Rusak,Perlu Maintenance',
            'lokasi_penyimpanan' => 'nullable|string|max:100',
            'harga_beli' => 'required|integer|min:0',
            'harga_jual_umum' => 'required|integer|min:0',
            'harga_jual_bpjs' => 'required|integer|min:0',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'catatan' => 'nullable|string',
            'status' => 'required|in:Aktif,Nonaktif,Rusak,Dalam Perbaikan',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kode_alkes.required' => 'Kode alkes wajib diisi',
            'kode_alkes.unique' => 'Kode alkes sudah digunakan',
            'nama_alkes.required' => 'Nama alkes wajib diisi',
            'satuan.required' => 'Satuan wajib diisi',
            'kategori.required' => 'Kategori wajib dipilih',
            'stok_minimal.required' => 'Stok minimal wajib diisi',
            'jumlah_stok.required' => 'Jumlah stok wajib diisi',
            'kondisi.required' => 'Kondisi wajib dipilih',
            'status.required' => 'Status wajib dipilih',
            'harga_beli.required' => 'Harga beli wajib diisi',
            'harga_jual_umum.required' => 'Harga jual umum wajib diisi',
            'harga_jual_bpjs.required' => 'Harga jual BPJS wajib diisi',
            'tanggal_kalibrasi_berikutnya.after_or_equal' => 'Tanggal kalibrasi berikutnya harus setelah atau sama dengan tanggal kalibrasi terakhir',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('kode_alkes')) {
            $this->merge([
                'kode_alkes' => strtoupper($this->kode_alkes),
            ]);
        }
    }
}
