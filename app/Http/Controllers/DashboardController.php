<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $barang = session('barang', null);
    
        if ($barang) {
            Log::info('Barang diambil dari session: ' . json_encode($barang));
        } else {
            Log::info('Session "barang" kosong atau tidak ada.');
        }
    
        // Periksa apakah pengguna sudah login
        if (!session('is_logged_in')) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }
    
        // Tampilkan halaman dashboard
        return view('dashboard', compact('barang'));
    }
    
    public function createBarang()
    {
        return view('barang.create');
    }
}
