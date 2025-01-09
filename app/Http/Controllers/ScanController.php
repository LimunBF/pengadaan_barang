<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Barang;
use Zxing\QrReader; // Import namespace dari library

class ScanController extends Controller
{
    /**
     * Proses hasil scan QR code.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

            // Hapus file sementara
            Storage::disk('public')->delete($filePath);

            // Validasi apakah teks hasil decode kosong
            if (!$decodedText || trim($decodedText) === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid atau tidak dapat dibaca.',
                ]);
            }

            // Cari barang berdasarkan id_barang (hasil decode)
            $barang = Barang::where('id_barang', $decodedText)->where('kondisi', 'ada')->first();

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
