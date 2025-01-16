<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Illuminate\Support\Facades\Log;
use App\Services\QRCodeService;

class DaftarBarangController extends Controller
{

    public function index(Request $request)
    {
        // Ambil semua data barang dari database
        $barang = Barang::all();
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

    private $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function update(Request $request, $id_barang)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'nullable|string|max:255',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $barang = Barang::findOrFail($id_barang);

        $dataUpdate = [
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
            'kode_qr' => json_encode([
                'id_barang' => $barang->id_barang,
                'nama_barang' => $request->nama_barang,
                'jenis_barang' => $request->jenis_barang,
            ]),
        ];

        if ($request->hasFile('foto_barang')) {
            if ($barang->foto_barang) {
                $relativePath = str_replace(asset('storage'), '', $barang->foto_barang);
                $oldImagePath = storage_path('app/public' . $relativePath);

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            $fotoPath = $request->file('foto_barang')->store('barang_photos', 'public');
            $fotoUrl = asset('storage/' . $fotoPath);
            $dataUpdate['foto_barang'] = $fotoUrl;
        }

        $barang->update($dataUpdate);

        // Hapus file QR Code lama
        $this->qrCodeService->deleteQRCode($barang->id_barang);

        // Generate QR Code baru
        $qrCodePath = $this->qrCodeService->generateQRCode(
            json_encode(['id_barang' => $barang->id_barang]),
            $barang->id_barang,
            $barang->nama_barang
        );

        $barang->update(['qr_code_path' => $qrCodePath]);

        session()->forget('edit_id');

        return redirect()->route('barang.index')
            ->with('success', 'Barang dan QR Code berhasil diperbarui.');
    }

    public function destroy($id_barang)
    {
        // Cari barang berdasarkan ID
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();
    
        // Update kolom kondisi menjadi 'dihapus'
        $barang->update([
            'kondisi' => 'dihapus',
        ]);
    
        // Pesan sukses
        return redirect()
            ->back()
            ->with('success', 'Barang berhasil diubah kondisinya menjadi dihapus.');
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
