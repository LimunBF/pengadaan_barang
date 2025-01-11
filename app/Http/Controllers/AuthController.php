<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $validEmail = 'admin';
    private $validPassword = 'admin';

    public function showLoginForm()
    {
        // Jika sudah login, redirect ke dashboard
        if (session('is_logged_in', false)) {
            return redirect()->route('barcode.data');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        Log::info(__('validation.required'));

        // Periksa email dan password statik
        if ($request->email === $this->validEmail && $request->password === $this->validPassword) {
            // Simpan informasi login di session
            session(['is_logged_in' => true]);
            Log::info('Session is_logged_in set: ' . session('is_logged_in'));
    
            // Redirect ke halaman barcode-data
            return redirect()->route('barcode.data')->with('success', 'Login berhasil!');
        }
    
        // Jika gagal login
        Log::info('Login failed.');
        return back()->withErrors(['email' => 'Username atau password salah.'])->withInput();
    }

    public function logout()
    {
        // Hapus informasi login dari session
        session()->forget('is_logged_in');

        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}
