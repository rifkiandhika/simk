<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class PinVerificationController extends Controller
{
    /**
     * Tampilkan form PIN verification
     */
    public function showVerificationForm()
    {
        // Jika sudah terverifikasi, redirect ke dashboard
        if (session('pin_verified')) {
            return redirect()->route('dashboard');
        }

        return view('auth.pin-verification-modal');
    }

    /**
     * Proses verifikasi PIN (AJAX)
     */
    public function verify(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6'
        ], [
            'pin.required' => 'PIN wajib diisi',
            'pin.digits' => 'PIN harus 6 digit angka'
        ]);

        $user = Auth::user();
        $inputPin = $request->pin;

        // ✅ STEP 1: Ambil role user yang login
        $userRole = $user->roles()->first();
        
        if (!$userRole) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki role. Hubungi administrator.'
            ], 422);
        }

        // ✅ STEP 2: Ambil PIN prefix dari role
        $rolePin = $userRole->pin;

        if (!$rolePin) {
            return response()->json([
                'success' => false,
                'message' => "Role {$userRole->name} belum memiliki PIN. Hubungi administrator."
            ], 422);
        }

        // ✅ STEP 3: Validasi 3 digit pertama PIN input dengan PIN role
        $rolePinPrefix = $rolePin; // PIN role sudah 3 digit (contoh: '000')
        $inputPinPrefix = substr($inputPin, 0, 3); // Ambil 3 digit pertama dari input

        if ($inputPinPrefix !== $rolePinPrefix) {
            // Cari role mana yang punya prefix ini
            $pinOwnerRole = Role::where('pin', $inputPinPrefix)->first();
            
            if ($pinOwnerRole) {
                return response()->json([
                    'success' => false,
                    'message' => "PIN tidak sesuai dengan role '{$userRole->name}'. PIN yang Anda masukkan adalah untuk role '{$pinOwnerRole->name}'."
                ], 422);
            }
            
            return response()->json([
                'success' => false,
                'message' => "PIN tidak sesuai dengan role '{$userRole->name}'. Silakan gunakan PIN yang benar."
            ], 422);
        }

        // ✅ STEP 4: Cari karyawan berdasarkan PIN lengkap (6 digit)
        $karyawan = Karyawan::where('pin', $inputPin)
            ->where('status_aktif', 'Aktif')
            ->first();

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'PIN tidak terdaftar pada karyawan aktif manapun.'
            ], 422);
        }

        // ✅ STEP 5: Pastikan karyawan punya user account
        $karyawanUser = $karyawan->user;
        
        if (!$karyawanUser) {
            return response()->json([
                'success' => false,
                'message' => "Karyawan {$karyawan->nama_lengkap} belum memiliki akun user."
            ], 422);
        }

        // ✅ STEP 6: Ambil role karyawan
        $karyawanRole = $karyawanUser->roles()->first();
        
        if (!$karyawanRole) {
            return response()->json([
                'success' => false,
                'message' => "Karyawan {$karyawan->nama_lengkap} belum memiliki role."
            ], 422);
        }

        // ✅ STEP 7: Validasi role karyawan harus sama dengan role user login
        if ($karyawanRole->name !== $userRole->name) {
            return response()->json([
                'success' => false,
                'message' => "Karyawan {$karyawan->nama_lengkap} memiliki role '{$karyawanRole->name}', tidak sesuai dengan role login '{$userRole->name}'."
            ], 422);
        }

        // ✅ SEMUA VALIDASI LOLOS - Simpan ke session
        session([
            'pin_verified' => true,
            'pin_verified_at' => now(),
            'pin_karyawan_id' => $karyawan->id_karyawan,
            'pin_karyawan_nama' => $karyawan->nama_lengkap,
            'pin_verified_role' => $karyawanRole->name,
            'pin_karyawan_nip' => $karyawan->nip,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Selamat datang, {$karyawan->nama_lengkap}!",
            'data' => [
                'nama' => $karyawan->nama_lengkap,
                'role' => $karyawanRole->name,
                'nip' => $karyawan->nip,
            ]
        ]);
    }

    /**
     * Logout dari PIN verification (tetap login user, hapus session PIN)
     */
    public function logoutPin()
    {
        session()->forget([
            'pin_verified',
            'pin_verified_at',
            'pin_karyawan_id',
            'pin_karyawan_nama',
            'pin_verified_role',
            'pin_karyawan_nip'
        ]);

        return redirect()->route('pin.verify.form')
            ->with('success', 'PIN telah di-logout. Silakan verifikasi ulang.');
    }

    /**
     * Dapatkan daftar role yang diizinkan untuk user tertentu
     */
    private function getAllowedRolesForUser($user): array
    {
        // Mapping username/unit ke role yang diizinkan
        $mapping = [
            'apotik' => ['Apoteker', 'Superadmin'],
            'kasir' => ['Kasir', 'Superadmin'],
            'gudang' => ['Kepala Gudang', 'Staff Gudang', 'Superadmin'],
            'direktur' => ['Direktur', 'Superadmin'],
            'superadmin' => ['Superadmin'],
            // tambahkan mapping lainnya
        ];

        $username = strtolower($user->username);

        return $mapping[$username] ?? [];
    }

    /**
     * Redirect berdasarkan role
     */
    private function redirectToRoleDashboard(string $role)
    {
        $redirectMap = [
            'Apoteker' => route('apotik.index'),
            'Kasir' => route('tagihans.dashboard'),
            'Kepala Gudang' => route('gudangs.index'),
            'Staff Gudang' => route('gudangs.stock'),
            'Direktur' => route('dashboard'),
            'Superadmin' => route('dashboard'),
        ];

        $route = $redirectMap[$role] ?? route('dashboard');

        return redirect($route)->with('success', "Selamat datang, " . session('pin_karyawan_nama'));
    }
}
