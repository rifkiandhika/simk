<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IgdStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'no_rm' => 'required|string|exists:pasiens,no_rm',
            'waktu_datang' => 'nullable|date',
            'cara_datang' => 'required|in:Jalan Kaki,Ambulans,Rujukan',
            'keluhan_utama' => 'nullable|string',
            'tingkat_kesadaran' => 'nullable|in:CM,Apatis,Somnolen,Sopor,Koma',
            'status_triase' => 'nullable|in:Merah,Kuning,Hijau,Hitam',
            'tindakan_awal' => 'nullable|string',
            'dokter_jaga' => 'required|exists:dokters,id_dokter',
            'perawat_jaga' => 'required|exists:perawats,id_perawat',
            
            // Triase data
            'triase.prioritas' => 'nullable|in:P1 Merah,P2 Kuning,P3 Hijau,P4 Hitam',
            'triase.keluhan' => 'nullable|string',
            'triase.vital_sign_ringkas' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'no_rm.required' => 'Nomor RM wajib diisi',
            'no_rm.exists' => 'Nomor RM tidak ditemukan',
            'cara_datang.required' => 'Cara datang wajib dipilih',
            'cara_datang.in' => 'Cara datang tidak valid',
            'dokter_jaga.required' => 'Dokter jaga wajib dipilih',
            'dokter_jaga.exists' => 'Dokter tidak ditemukan',
            'perawat_jaga.required' => 'Perawat jaga wajib dipilih',
            'perawat_jaga.exists' => 'Perawat tidak ditemukan',
        ];
    }
}

