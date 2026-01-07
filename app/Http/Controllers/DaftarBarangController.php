<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Gudang; // Tambahkan Model Gudang untuk sinkronisasi
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Log;
use App\Services\QRCodeService;
use App\Services\AuthService; 
use Illuminate\Support\Facades\Storage;

class DaftarBarangController extends Controller
{
    private $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
        // AuthService::checkLogin(); // Uncomment jika sudah aktif
    }

    public function index(Request $request)
    {
        // Reset edit mode jika berasal dari halaman lain
        if ($request->headers->get('referer') && parse_url($request->headers->get('referer'), PHP_URL_PATH) !== '/barang') {
            session()->forget('edit_id');
        }
    
        // Filter kondisi barang (default: 'ada')
        $filter = $request->get('filter', 'ada');
        $barang = Barang::where('kondisi', $filter)->get();
    
        return view('daftar_barang.index', compact('barang', 'filter'));
    }
    
    public function enableEditMode($id)
    {
        session(['edit_id' => $id]);
        return redirect()->back(); // Kembali ke halaman yang sama (refresh)
    }

    public function update(Request $request, $id_barang)
    {
        // 1. Validasi
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'nullable|string|max:255',
            'foto_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $barang = Barang::findOrFail($id_barang);

        // 2. Siapkan Data Update Dasar
        $dataUpdate = [
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
            // Update isi JSON QR (walaupun path file QR nanti digenerate ulang)
            'kode_qr' => json_encode([
                'id_barang' => $barang->id_barang,
                'nama_barang' => $request->nama_barang,
                'jenis_barang' => $request->jenis_barang,
            ]),
        ];

        // 3. Logika Upload Gambar (Simpan Relative Path)
        if ($request->hasFile('foto_barang')) {
            // Hapus gambar lama jika ada
            if ($barang->foto_barang) {
                // Bersihkan path dari URL localhost jika ada sisa data lama
                $oldPath = str_replace(url('storage') . '/', '', $barang->foto_barang);
                $oldPath = str_replace('/storage/', '', $oldPath); // Jaga-jaga double slash
                
                // Hapus fisik file menggunakan Storage Disk Public
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // Simpan gambar baru (hasilnya: 'barang_photos/xxx.jpg')
            $fotoPath = $request->file('foto_barang')->store('barang_photos', 'public');
            
            // Simpan path relatif ke database (BUKAN URL LENGKAP)
            $dataUpdate['foto_barang'] = $fotoPath;
        }

        // 4. Eksekusi Update Barang
        $barang->update($dataUpdate);

        // 5. Sinkronisasi Nama & Jenis ke Tabel Gudang
        Gudang::where('id_barang', $id_barang)->update([
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
        ]);

        // 6. Regenerate QR Code (Karena Nama Barang mungkin berubah)
        // Hapus file QR lama
        if ($barang->qr_code_path) {
             // Bersihkan path
             $oldQrPath = str_replace(url('storage') . '/', '', $barang->qr_code_path);
             if (Storage::disk('public')->exists($oldQrPath)) {
                 Storage::disk('public')->delete($oldQrPath);
             }
        }

        // Generate baru
        $namaFileAman = Str::limit(Str::slug($request->nama_barang), 50, '') . '-' . $barang->id_barang . '.png';
        $qrCodePath = $this->qrCodeService->generateQRCode(
            json_encode(['id_barang' => $barang->id_barang]),
            $namaFileAman, // Gunakan nama file yang bersih
            $request->nama_barang // Label pada gambar
        );

        $barang->update(['qr_code_path' => $qrCodePath]);

        session()->forget('edit_id');

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui (Foto, QR, dan Data Gudang sinkron).');
    }

    public function destroy($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();
        
        // Soft Delete (Ubah status jadi dihapus)
        $barang->update([
            'kondisi' => 'dihapus',
        ]);
    
        return redirect()->back()
            ->with('success', 'Barang berhasil diarsipkan (kondisi dihapus).');
    }    

    public function cancelEdit($id)
    {
        session()->forget('edit_id');
        return redirect()->route('barang.index');
    }
    
    public function downloadQRCode($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();

        $path = $barang->qr_code_path;

        if (empty($path)) {
            return back()->with('error', 'Data QR Code belum digenerate.');
        }

        // Bersihkan Path agar aman
        $cleanPath = str_replace(['/storage/', 'storage/'], '', $path);
        $cleanPath = ltrim($cleanPath, '/'); 

        // Cek via Storage Facade
        if (Storage::disk('public')->exists($cleanPath)) {
            $fullPath = Storage::disk('public')->path($cleanPath);
            $downloadName = Str::slug($barang->nama_barang) . '-' . $barang->id_barang . '.png';
            
            return response()->download($fullPath, $downloadName);
        }

        Log::error("Gagal download. File fisik hilang di: " . $cleanPath);
        return back()->with('error', "File QR Code fisik tidak ditemukan. Silakan edit barang untuk generate ulang.");
    }
}