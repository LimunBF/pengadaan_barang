<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Log; // Tambahkan ini
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class DaftarBarangController extends Controller
{
    public function index()
    {
        // Ambil semua data barang
        $barang = Barang::all();

        // Kirim data ke view
        return view('daftar_barang.index', compact('barang'));
    }

    public function edit($id_barang)
    {
        $barang = Barang::findOrFail($id_barang);
        return view('barang.index', compact('barang'));
    }


    public function update(Request $request, $id_barang)
    {
        // Validasi input
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'deskripsi_barang' => 'nullable|string',
        ]);
    
        // Ambil data barang
        $barang = Barang::findOrFail($id_barang);
    
        // Hapus file QR Code lama jika ada
        $oldQrCodePath = public_path("qr_codes/{$barang->id_barang}.png");
        if (file_exists($oldQrCodePath)) {
            unlink($oldQrCodePath);
        }
    
        // Update data barang
        $barang->update([
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
            'deskripsi_barang' => $request->deskripsi_barang,
        ]);
    
        // Generate QR Code baru
        $qrCodePath = $this->generateQRCode($barang);
    
        // Update QR Code path di database
        $barang->update([
            'kode_qr' => json_encode([
                'id_barang' => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'jenis_barang' => $barang->jenis_barang,
                'deskripsi_barang' => $barang->deskripsi_barang,
            ]),
            'qr_code_path' => $qrCodePath,
        ]);
    
        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }    
    
    public function generateQRCode(Barang $barang)
    {
        // Siapkan data untuk QR Code (format JSON)
        $qrData = json_encode([
            'id_barang' => $barang->id_barang,
            'nama_barang' => $barang->nama_barang,
            'jenis_barang' => $barang->jenis_barang,
            'deskripsi_barang' => $barang->deskripsi_barang ?? '-',
        ]);

        // Buat direktori untuk menyimpan QR Code jika belum ada
        if (!is_dir(public_path('qr_codes'))) {
            mkdir(public_path('qr_codes'), 0755, true);
        }

        // Tentukan lokasi file QR Code
        $filePath = public_path("qr_codes/{$barang->id_barang}.png");

        // Gunakan Endroid QR Code untuk membangun QR Code
        $qrCode = new QrCode(
            data: $qrData,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $writer = new PngWriter();
        $writer->write($qrCode)->saveToFile($filePath);

        // Kembalikan URL path gambar QR Code
        return asset("qr_codes/{$barang->id_barang}.png");
    }


    public function destroy($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();
        $barang->delete();

        return redirect()->back()->with('success', 'Data barang berhasil dihapus.');
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
