<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $validEmail = 'admin';
    private $validPassword = 'admin';

    /**
     * Menampilkan halaman login.
     * Jika pengguna sudah login, langsung redirect ke halaman dashboard.
     */
    public function showLoginForm()
    {
        // Jika sudah login, redirect ke halaman barcode.data
        if (session('is_logged_in', false)) {
            return redirect()->route('barcode.data');
        }

        return view('auth.login');
    }

    /**
     * Proses login pengguna.
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        Log::info('Input login divalidasi.');

        // Periksa email dan password (statik dalam contoh ini)
        if ($request->email === $this->validEmail && $request->password === $this->validPassword) {
            // Simpan informasi login di session
            session([
                'is_logged_in' => true,
                'login_time' => now()->toDateTimeString()  // Simpan waktu login sebagai string
            ]);
            Log::info('Session is_logged_in set. Login_time: ' . session('login_time'));

            // Redirect ke halaman barcode-data
            return redirect()->route('barcode.data')->with('success', 'Login berhasil!');
        }

        // Jika gagal login
        Log::warning('Login failed for email: ' . $request->email);
        return back()->withErrors(['email' => 'Username atau password salah.'])->withInput();
    }

    /**
     * Logout pengguna dan hapus semua session.
     */
    public function logout()
    {
        // Catat aktivitas logout
        Log::info('User logged out. Previous login_time: ' . session('login_time'));

        // Hapus informasi login dari session
        session()->flush();

        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}
