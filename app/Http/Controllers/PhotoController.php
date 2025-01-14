<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;

class PhotoController extends Controller
{
    public function store(Request $request)
    {
        $imageData = $request->input('image'); // Ambil data gambar dari request
        $fileName = 'photo_' . time() . '.png'; // Nama file unik

        // Decode data URL
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $image = base64_decode($image);

        // Path lokal di folder public/photos
        $localPath = public_path('photos/' . $fileName);

        // Pastikan folder photos ada
        if (!file_exists(public_path('photos'))) {
            mkdir(public_path('photos'), 0777, true);
        }

        // Simpan file ke folder lokal
        file_put_contents($localPath, $image);

        // Simpan data ke tabel transaksi
        Transaksi::create([
            'id_barang' => $request->input('id_barang'), // Pastikan data ini dikirimkan dari frontend
            'tipe_transaksi' => $request->input('tipe_transaksi'), // "masuk" atau "keluar"
            'kuantitas' => $request->input('kuantitas'),
            'nama_pengirim_penerima' => $request->input('nama_pengirim_penerima'),
            'catatan' => $request->input('catatan'),
            'photo' => asset('photos/' . $fileName), // Link ke foto yang disimpan
        ]);

        return response()->json([
            'message' => 'Transaksi berhasil disimpan!',
            'file' => asset('photos/' . $fileName), // URL file untuk referensi
        ]);
    }
}
