<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Log;

class DaftarBarangController extends Controller
{
    public function index(Request $request)
    {
        // Periksa apakah URL sebelumnya bukan /barang
        if ($request->headers->get('referer') && parse_url($request->headers->get('referer'), PHP_URL_PATH) !== '/barang') {
            // Hapus session edit_id
            session()->forget('edit_id');
        }
    
        // Ambil semua data barang
        $barang = Barang::all();
    
        // Kirim data ke view
        return view('daftar_barang.index', compact('barang'));
    }

    public function enableEditMode($id)
    {
        // Simpan ID barang ke dalam session untuk mengaktifkan mode edit
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
        ]);

        // Ambil data barang
        $barang = Barang::findOrFail($id_barang);

        // Update data barang sekaligus kode QR dalam satu operasi
        $barang->update([
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
            'deskripsi_barang' => $request->deskripsi_barang,
            'kode_qr' => json_encode([
                'id_barang' => $barang->id_barang,
                'nama_barang' => $request->nama_barang,
                'jenis_barang' => $request->jenis_barang,
                'deskripsi_barang' => $request->deskripsi_barang ?? '-',
            ]),
        ]);

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
        // Periksa apakah barang ada (opsional jika tidak diperlukan, bisa dihapus)
        $barang = Barang::find($id);
        if (!$barang) {
            return redirect()->route('barang.index')->with('error', 'Data barang tidak ditemukan.');
        }
    
        // Hapus session edit_id
        session()->forget('edit_id');
    
        // Kembalikan ke halaman daftar barang
        return redirect()->route('barang.index');
    }
    
    
    public function downloadQRCode($id_barang)
    {
        try {
            // Cari barang berdasarkan id_barang
            $barang = Barang::where('id_barang', $id_barang)->firstOrFail();

            // Lokasi file QR Code berdasarkan id_barang
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
