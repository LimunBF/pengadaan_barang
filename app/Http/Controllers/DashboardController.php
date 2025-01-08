<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Periksa apakah pengguna sudah login
        if (!session('is_logged_in')) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Tampilkan halaman dashboard
        return view('dashboard');
    }
    
    public function createBarang()
    {
        return view('barang.create');
    }
}
