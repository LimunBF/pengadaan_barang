<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;

class PhotoController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_barang' => 'required|string|max:50',
            'tipe_transaksi' => 'required|in:masuk,keluar',
            'kuantitas' => 'required|integer|min:1',
            'nama_pengirim_penerima' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'image_data' => 'required|string', // Base64 image
        ]);

        // Decode Base64 Image
        $imageData = $request->input('image_data');
        $fileName = 'photo_' . time() . '.png'; // Nama file unik
        $image = str_replace('data:image/png;base64,', '', $imageData);
        $image = str_replace(' ', '+', $image);
        $image = base64_decode($image);

        // Simpan gambar ke folder lokal
        $filePath = public_path('photos/' . $fileName);

        if (!file_exists(public_path('photos'))) {
            mkdir(public_path('photos'), 0777, true);
        }

        file_put_contents($filePath, $image);

        // Simpan data ke tabel transaksi
        $photoUrl = asset('photos/' . $fileName);
        Transaksi::create([
            'id_barang' => $request->input('id_barang'),
            'tipe_transaksi' => $request->input('tipe_transaksi'),
            'kuantitas' => $request->input('kuantitas'),
            'nama_pengirim_penerima' => $request->input('nama_pengirim_penerima'),
            'catatan' => $request->input('catatan'),
            'photo' => $photoUrl,
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan dengan foto.');
    }
}
