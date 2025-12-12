<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RealRashid\SweetAlert\Facades\Alert;

class LoginController extends Controller
{
    /**
     * Display login form
     */
    public function index()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }

        return view('login.index');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau email harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        // Check rate limiting (5 attempts per minute)
        $key = Str::lower($request->input('login')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'login' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ]);
        }

        // Attempt login with username or email
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        // Add status check
        $credentials['status'] = 'Aktif';

        // Attempt authentication
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Clear rate limiter
            RateLimiter::clear($key);

            // Update last login
            $user = Auth::user();
            $user->update(['last_login' => now()]);

            // Redirect based on role
            return $this->redirectBasedOnRole($user);
        }

        // Increment rate limiter
        RateLimiter::hit($key, 60);

        // Failed login
        throw ValidationException::withMessages([
            'login' => 'Username/Email atau password salah, atau akun Anda tidak aktif.',
        ]);
    }

    /**
     * Redirect user based on their role
     */
    protected function redirectBasedOnRole($user)
    {
        $role = $user->roles->first();

        if (!$role) {
            return redirect('/dashboard')->with('success', 'Login berhasil!');
        }

        // Define role-specific redirects
        $roleRedirects = [
            'Superadmin' => '/dashboard',
            'Admin' => '/dashboard',
            'Direktur' => '/dashboard',
            'Dokter' => '/rekam-medis',
            'Perawat' => '/vital-signs',
            'Apoteker' => '/obatrs',
            'Analis Lab' => '/reagensia',
            'Radiografer' => '/pemeriksaan-radiologi',
            'Kasir' => '/tagihan',
            'Gudang' => '/gudangs',
            'Registrasi' => '/registrasi',
            'Staff' => '/dashboard',
        ];

        $redirectTo = $roleRedirects[$role->name] ?? '/dashboard';

        Alert::success('success', 'Selamat datang, ' . $user->name . '!');
        return redirect($redirectTo);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda telah logout.');
    }

    /**
     * Show forgot password form
     */
    public function forgotPassword()
    {
        return view('login.forgot-password');
    }

    /**
     * Handle forgot password request
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar',
        ]);

        // TODO: Implement password reset logic
        // For now, just return success message

        return back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    }
}
