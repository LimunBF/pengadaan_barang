<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Log; // Tambahkan ini

class DaftarBarangController extends Controller
{
    public function index()
    {
        // Ambil semua data barang
        $barang = Barang::all();

        // Kirim data ke view
        return view('daftar_barang.index', compact('barang'));
    }

    public function downloadQRCode($id_barang)
    {
        try {
            // Cari barang berdasarkan id_barang
            $barang = Barang::where('id_barang', $id_barang)->firstOrFail();

            // Lokasi file QR Code berdasarkan data barang
            $filePath = public_path("qr_codes/{$id_barang}.png");

            // Logging untuk debugging
            Log::info("Trying to download file at: {$filePath}");

            // Cek apakah file ada
            if (!file_exists($filePath)) {
                Log::error("File not found: {$filePath}");
                return redirect()->back()->with('error', 'QR Code tidak ditemukan.');
            }

            // Kembalikan file untuk diunduh
            return response()->download($filePath, "{$id_barang}_qrcode.png");
        } catch (\Exception $e) {
            // Tangkap error jika terjadi
            Log::error("Error downloading QR Code: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mendownload QR Code.');
        }
    }   
}
