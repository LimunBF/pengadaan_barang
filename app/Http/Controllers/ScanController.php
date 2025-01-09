<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Barang;
use Zxing\QrReader; // Pastikan Anda menginstal library QR reader

class ScanController extends Controller
{
    /**
     * Proses hasil scan QR code.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function processScan(Request $request)
    {
        // Validasi input file
        $request->validate([
            'qr_image' => 'required|image|mimes:png,jpg,jpeg|max:2048', // Gambar harus berupa PNG, JPG, atau JPEG
        ]);

        // Simpan gambar hasil upload sementara
        $uploadedFile = $request->file('qr_image');
        $path = $uploadedFile->store('temp', 'public');

        // Decode QR code dari gambar
        $fullPath = storage_path('app/public/' . $path);
        $qrcodeReader = new QrReader($fullPath);
        $decodedText = $qrcodeReader->text(); // Mendapatkan teks dari QR code

        // Hapus file sementara
        Storage::disk('public')->delete($path);

        if (!$decodedText) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau tidak dapat dibaca.',
            ]);
        }

        // Cari QR code di database
        $barang = Barang::where('kode_qr', $decodedText)->first();

        if ($barang) {
            return response()->json([
                'success' => true,
                'message' => 'QR Code valid. Data ditemukan.',
                'data' => $barang,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak ditemukan di database.',
            ]);
        }
    }
}
