<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Log;

class DaftarBarangController extends Controller
{
    public function index(Request $request)
    {
        // Reset edit mode jika berasal dari halaman lain
        if ($request->headers->get('referer') && parse_url($request->headers->get('referer'), PHP_URL_PATH) !== '/barang') {
            session()->forget('edit_id');
        }
    
        // Filter kondisi barang (default: 'ada')
        $filter = $request->get('filter', 'ada');
        $barang = Barang::where('kondisi', $filter)->get();
    
        // Kirim data ke view
        return view('daftar_barang.index', compact('barang', 'filter'));
    }
    
    public function enableEditMode($id)
    {
        session(['edit_id' => $id]);
        return redirect()->back();
    }
    
    

    public function update(Request $request, $id_barang)
    {
        // Validasi input
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'deskripsi_barang' => 'nullable|string',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Tambahkan validasi untuk gambar
        ]);

        // Ambil data barang
        $barang = Barang::findOrFail($id_barang);

        // Update data barang
        $dataUpdate = [
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
            'deskripsi_barang' => $request->deskripsi_barang,
            'kode_qr' => json_encode([
                'id_barang' => $barang->id_barang,
                'nama_barang' => $request->nama_barang,
                'jenis_barang' => $request->jenis_barang,
                'deskripsi_barang' => $request->deskripsi_barang ?? '-',
            ]),
        ];

        // Jika ada file gambar baru, proses gambar
        if ($request->hasFile('foto_barang')) {
            Log::info('File gambar diterima: ' . $request->file('foto_barang')->getClientOriginalName());

            // Hapus gambar lama jika ada
            if ($barang->foto_barang) {
                $relativePath = str_replace(asset('storage'), '', $barang->foto_barang); // Hapus bagian URL
                $oldImagePath = storage_path('app/public' . $relativePath);

                Log::info('Path gambar lama: ' . $oldImagePath);
                if (file_exists($oldImagePath)) {
                    Log::info('Gambar lama ditemukan dan akan dihapus.');
                    unlink($oldImagePath); // Hapus file gambar lama
                } else {
                    Log::error('Gambar lama tidak ditemukan di path: ' . $oldImagePath);
                }
            }

            // Simpan file gambar baru
            $fotoPath = $request->file('foto_barang')->store('barang_photos', 'public');
            $fotoUrl = asset('storage/' . $fotoPath);

            // Tambahkan URL gambar baru ke data yang akan diupdate
            $dataUpdate['foto_barang'] = $fotoUrl;
        }

        // Perbarui database dengan data baru
        $barang->update($dataUpdate);

        // Reset session edit_id setelah update
        session()->forget('edit_id');

        return redirect()
            ->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();

        // Hapus file QR Code terkait barang jika ada
        $qrCodePath = public_path("qr_codes/{$barang->id_barang}.png");
        if (file_exists($qrCodePath)) {
            unlink($qrCodePath);
        }

        $barang->delete();

        return redirect()->back()->with('success', 'Data barang berhasil dihapus.');
    }

    public function cancelEdit($id)
    {
        session()->forget('edit_id');
        return redirect()->route('barang.index');
    }
    
    public function downloadQRCode($id_barang)
    {
        try {
            // Cari barang berdasarkan id_barang
            $barang = Barang::where('id_barang', $id_barang)->firstOrFail();
    
            // Lokasi file QR Code di folder storage
            $filePath = storage_path("app/public/barang_qr_codes/{$id_barang}.png");
    
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
