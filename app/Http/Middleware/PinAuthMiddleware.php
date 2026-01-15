<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PinAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Cek apakah PIN sudah diverifikasi
        if (!session('pin_verified')) {
            // Jika request adalah AJAX, return JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'PIN verification required',
                    'pin_required' => true
                ], 403);
            }
            
            // Redirect ke dashboard dengan flag PIN required
            // Modal PIN akan muncul otomatis di dashboard
            return redirect()->route('dashboard')
                ->with('pin_required', true)
                ->with('info', 'Silakan masukkan PIN untuk melanjutkan');
        }

        // Validasi role-based access (opsional, hanya jika Anda ingin restrict berdasarkan role)
        if (session()->has('pin_verified_role')) {
            $allowedRole = session('pin_verified_role');
            $currentRoute = $request->route()->getName();
            
            // Daftar route prefix untuk setiap role
            $roleRoutes = [
                'Apoteker' => ['apotik', 'stock_apotiks', 'pasiens', 'obat'],
                'Kasir' => ['tagihan', 'po', 'tagihans'],
                'Kepala Gudang' => ['gudangs', 'po', 'stock', 'history-gudang'],
                'Admin Gudang' => ['gudangs', 'stock', 'permintaan'],
                'Direktur' => ['*'], // akses semua
                'Superadmin' => ['*'], // akses semua
            ];

            // Jika bukan role dengan akses penuh, validasi route
            if (!in_array($allowedRole, ['Superadmin', 'Direktur'])) {
                $hasAccess = false;
                
                if (isset($roleRoutes[$allowedRole])) {
                    foreach ($roleRoutes[$allowedRole] as $prefix) {
                        // Cek jika route dimulai dengan prefix yang diizinkan
                        if ($prefix === '*' || str_starts_with($currentRoute, $prefix)) {
                            $hasAccess = true;
                            break;
                        }
                    }
                }

                // Tambahkan akses ke route umum yang semua role bisa akses
                $commonRoutes = ['dashboard', 'logout', 'pin.verify', 'pin.logout', 'notifications'];
                foreach ($commonRoutes as $commonRoute) {
                    if (str_starts_with($currentRoute, $commonRoute)) {
                        $hasAccess = true;
                        break;
                    }
                }

                // Jika tidak punya akses
                if (!$hasAccess) {
                    // Jika AJAX request
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Anda tidak memiliki akses ke halaman ini'
                        ], 403);
                    }

                    // Redirect ke dashboard dengan pesan error
                    return redirect()->route('dashboard')
                        ->with('error', 'Anda tidak memiliki akses ke halaman ini dengan role: ' . $allowedRole);
                }
            }
        }

        return $next($request);
    }
}