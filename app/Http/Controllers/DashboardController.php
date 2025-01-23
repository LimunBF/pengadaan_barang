<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\Barang;
use Illuminate\Support\Facades\Log;
use App\Services\AuthService; // Tambahkan import AuthService

class DashboardController extends Controller
{
    public function __construct()
    {
        AuthService::checkLogin(); // Panggil pengecekan login
    }

    public function index(Request $request)
    {
        $barang = session('barang', null);
    
        if ($barang) {
            // Ambil stok dari tabel Gudang berdasarkan id_barang
            $stokGudang = Gudang::where('id_barang', $barang->id_barang)->value('stok');
            $barang->stok = $stokGudang; // Tambahkan stok ke objek barang
            
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

    public function show($id)
    {
        // Mengambil data barang dari tabel gudang berdasarkan id_barang
        $barang = Gudang::where('id_barang', $id)->first();
    
        // Jika data barang tidak ditemukan
        if (!$barang) {
            return response()->json([
                'message' => 'Data barang tidak ditemukan',
            ], 404);
        }
    
        // Kembalikan data barang dalam format JSON
        return response()->json([
            'id_barang' => $barang->id_barang,
            'nama_barang' => $barang->nama_barang,
            'jenis_barang' => $barang->jenis_barang,
            'lokasi_rak' => $barang->lokasi_rak,
            'deskripsi_barang' => $barang->deskripsi_barang,
            'stok' => $barang->stok,
            'satuan' => $barang->satuan,
        ]);
    }
}