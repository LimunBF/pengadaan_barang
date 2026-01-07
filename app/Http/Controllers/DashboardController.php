<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\Petugas;
use Illuminate\Support\Facades\Log;
use App\Services\AuthService;

class DashboardController extends Controller
{
    public function __construct()
    {
        AuthService::checkLogin(); 
    }

    public function index(Request $request)
    {
        // 1. Ambil Barang dari Session (Hasil Scan)
        $barang = session('barang', null);
        
        // 2. Ambil Data Petugas untuk Dropdown
        $petugas = Petugas::all();
    
        if ($barang) {
            // Update stok terbaru dari database (biar real-time)
            $gudangInfo = Gudang::where('id_barang', $barang['id_barang'])->first();
            
            // Kita inject data terbaru ke object/array barang
            if ($gudangInfo) {
                $barang['stok'] = $gudangInfo->stok;
                $barang['lokasi_rak'] = $gudangInfo->lokasi_rak;
            } else {
                $barang['stok'] = 0;
                $barang['lokasi_rak'] = '-';
            }
            
            // Simpan balik ke session biar konsisten selama page reload
            session(['barang' => $barang]);
        }
    
        return view('dashboard', compact('barang', 'petugas'));
    }
    
    // Fitur hapus sesi (Tombol Batal/Clear jika ada)
    public function clearSession()
    {
        session()->forget('barang');
        return redirect()->route('dashboard');
    }
}