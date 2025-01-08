<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $validEmail = 'admin';
    private $validPassword = 'admin';

    public function showLoginForm()
    {
        // Jika sudah login, redirect ke dashboard
        if (session('is_logged_in')) {
            return redirect()->route('dashboard');
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

        // Periksa email dan password statik
        if ($request->email === $this->validEmail && $request->password === $this->validPassword) {
            // Simpan informasi login di session
            session(['is_logged_in' => true]);

            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        // Jika gagal login
        return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
    }

    public function logout()
    {
        // Hapus informasi login dari session
        session()->forget('is_logged_in');

        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}
