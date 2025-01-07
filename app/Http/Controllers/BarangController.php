<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\Log;

class BarangController extends Controller
{
    public function create()
    {
        // Ambil barang terakhir berdasarkan ID Barang
        $lastBarang = Barang::orderBy('id_barang', 'desc')->first();

        // Hitung ID Barang berikutnya
        $nextId = $lastBarang ? str_pad((int) substr($lastBarang->id_barang, -4) + 1, 4, '0', STR_PAD_LEFT) : '0001';

        // Kirim data ke view create.blade.php
        return view('barang.create', compact('nextId'));
    }

    public function generateQRCode(Barang $barang)
    {
        // Siapkan data untuk QR Code (format JSON)
        $qrData = json_encode([
            'id_barang' => $barang->id_barang,
            'nama_barang' => $barang->nama_barang,
            'jenis_barang' => $barang->jenis_barang,
            'deskripsi_barang' => $barang->deskripsi_barang,
        ]);
    
        // Buat direktori jika belum ada
        if (!is_dir(public_path('qr_codes'))) {
            mkdir(public_path('qr_codes'), 0755, true);
        }
    
        // Generate QR Code
        $qrCode = new QrCode(
            data: $qrData,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0), // Warna hitam
            backgroundColor: new Color(255, 255, 255) // Warna putih
        );
    
        // Tentukan lokasi file QR Code
        $filePath = public_path("qr_codes/{$barang->id_barang}.png");
    
        // Simpan QR Code sebagai gambar
        $writer = new PngWriter();
        $writer->write($qrCode)->saveToFile($filePath);
    
        // Kembalikan URL path gambar QR Code
        return asset("qr_codes/{$barang->id_barang}.png");
    }    

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_barang' => 'required|string|max:50|unique:barang',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'deskripsi_barang' => 'required|string',
        ]);
    
        // Simpan data barang
        $barang = Barang::create([
            'id_barang' => $request->id_barang,
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
            'deskripsi_barang' => $request->deskripsi_barang,
        ]);
    
        // Generate QR Code dan simpan path-nya
        $qrCodePath = $this->generateQRCode($barang);
    
        // Update kode_qr dan qr_code_path di database
        $barang->update([
            'kode_qr' => json_encode([
                'id_barang' => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'jenis_barang' => $barang->jenis_barang,
                'deskripsi_barang' => $barang->deskripsi_barang,
            ]),
            'qr_code_path' => $qrCodePath,
        ]);
    
        // Simpan id_barang di session untuk keperluan download
        session([
            'last_generated_id' => $barang->id_barang,
            'last_generated_qr_path' => $qrCodePath,
        ]);
    
        // Redirect dengan pesan sukses
        return redirect()
            ->route('barang.create')
            ->with('success', 'Barang berhasil ditambahkan.')
            ->with('qr_code_url', $qrCodePath);
    }    
    
    public function downloadQRCode($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();
        $filePath = public_path("qr_codes/{$id_barang}.png");
    
        Log::info("Checking file path: {$filePath}");
    
        if (!file_exists($filePath)) {
            Log::error("File not found: {$filePath}");
            return redirect()->back()->with('error', 'File QR Code tidak ditemukan.');
        }
    
        Log::info("File found, proceeding to download: {$filePath}");
    
        return response()->download($filePath, "{$id_barang}_qrcode.png")->deleteFileAfterSend(false);
    }

}