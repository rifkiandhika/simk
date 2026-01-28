<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IgdUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'keluhan_utama' => 'nullable|string',
            'tingkat_kesadaran' => 'nullable|in:CM,Apatis,Somnolen,Sopor,Koma',
            'status_triase' => 'nullable|in:Merah,Kuning,Hijau,Hitam',
            'tindakan_awal' => 'nullable|string',
            'status' => 'nullable|in:Dalam Perawatan,Pulang,Rawat Inap,Rujuk,Meninggal',
            'waktu_keluar' => 'nullable|date',
            'dokter_jaga' => 'nullable|exists:dokters,id_dokter',
            'perawat_jaga' => 'nullable|exists:perawats,id_perawat',
            'triase.prioritas' => 'nullable|in:P1 Merah,P2 Kuning,P3 Hijau,P4 Hitam',
            'triase.keluhan' => 'nullable|string',
            'triase.vital_sign_ringkas' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'status.in' => 'Status tidak valid',
            'dokter_jaga.exists' => 'Dokter tidak ditemukan',
            'perawat_jaga.exists' => 'Perawat tidak ditemukan',
        ];
    }
}
