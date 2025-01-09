<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; // Import namespace Log
use App\Models\Barang;
use Zxing\QrReader; // Import namespace untuk QR code reader

class ScanController extends Controller
{
    public function processScan(Request $request)
    {
        // Validasi input file
        $request->validate([
            'qr_image' => 'required|image|mimes:png,jpg,jpeg|max:2048', // Gambar harus berupa PNG, JPG, atau JPEG
        ]);

        // Simpan gambar hasil upload sementara
        $uploadedFile = $request->file('qr_image');
        $filePath = $uploadedFile->store('temp', 'public');

        // Full path ke file yang disimpan
        $fullPath = storage_path('app/public/' . $filePath);

        try {
            // Decode QR code menggunakan library
            $qrcode = new QrReader($fullPath); // Membaca QR code dari file
            $decodedText = $qrcode->text(); // Mendapatkan teks dari QR code

            // Log hasil decode untuk debugging
            Log::info('Decoded Text from QR Code: ' . $decodedText);

            // Hapus file sementara
            Storage::disk('public')->delete($filePath);

            // Validasi apakah teks hasil decode kosong
            if (!$decodedText || trim($decodedText) === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid atau tidak dapat dibaca.',
                ]);
            }

            // Cek apakah hasil decode berupa JSON
            $decodedJson = json_decode($decodedText, true);
            if ($decodedJson && isset($decodedJson['id_barang'])) {
                $id_barang = $decodedJson['id_barang'];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Format QR Code tidak valid. Tidak ditemukan id_barang.',
                ]);
            }

            // Cari barang berdasarkan id_barang
            $barang = Barang::where('id_barang', $id_barang)->where('kondisi', 'ada')->first();

            if ($barang) {
                return response()->json([
                    'success' => true,
                    'message' => 'QR Code valid. Barang ditemukan.',
                    'data' => $barang,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan atau sudah dihapus.',
                ]);
            }
        } catch (\Exception $e) {
            // Hapus file sementara jika terjadi error
            Storage::disk('public')->delete($filePath);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses QR Code: ' . $e->getMessage(),
            ]);
        }
    }
}
