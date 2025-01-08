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
        do {
            // Generate angka acak 4 digit
            $nextId = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Barang::where('id_barang', $nextId)->exists()); // Periksa apakah ID sudah ada di database
    
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
            'deskripsi_barang' => $barang->deskripsi_barang ?? '-', // Kosong jika null
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
            'deskripsi_barang' => 'nullable|string',
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

    public function getBarang($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->first();

        // Jika data tidak ditemukan, kembalikan pesan error
        if (!$barang) {
            return response()->json(['error' => 'Data barang tidak ditemukan'], 404);
        }

        // Kembalikan data barang dalam format JSON
        return response()->json($barang);
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
    public function scan(Request $request)
    {
        // Ambil data JSON dari barcode yang di-scan
        $jsonData = $request->input('barcode_data'); // Data JSON dari scanner

        // Cari data barang berdasarkan kode_qr di database
        $barang = Barang::where('kode_qr', $jsonData)->first();

        if (!$barang) {
            return abort(404, 'Barang tidak ditemukan.');
        }

        // Decode data JSON dari kolom kode_qr
        $decodedData = json_decode($barang->kode_qr, true);

        // Kirim data ke view untuk ditampilkan
        return view('barcode-scan-result', compact('decodedData'));
    }
    public function show($id_barang)
    {
        // Ambil data barang berdasarkan ID
        $barang = Barang::findOrFail($id_barang);

        // Kirim data barang ke view
        return view('barang-detail', compact('barang'));
    }
}
