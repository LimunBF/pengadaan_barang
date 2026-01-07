<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Exports\TransaksiExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Services\AuthService;

class TransaksiController extends Controller
{
    public function __construct()
    {
        AuthService::checkLogin();
    }

    // Menampilkan Halaman Riwayat (Tabel)
    public function index()
    {
        $transaksi = Transaksi::with('barang')->orderBy('waktu', 'desc')->get();
        
        $tanggalPertama = Transaksi::min('waktu') ? date('Y-m-d', strtotime(Transaksi::min('waktu'))) : date('Y-m-d');
        $tanggalTerakhir = Transaksi::max('waktu') ? date('Y-m-d', strtotime(Transaksi::max('waktu'))) : date('Y-m-d');

        return view('transaksi.index', compact('transaksi', 'tanggalPertama', 'tanggalTerakhir'));
    }

    // OTAK PEMINJAMAN (Menangani Form Dashboard)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'proses' => 'required|in:masuk,keluar',
                'kuantitas' => 'required|integer|min:1',
                'nama_pengirim_penerima' => 'required|string|max:255',
                'catatan' => 'nullable|string',
                'image_data' => 'nullable|string', // Ini string Base64 dari kamera dashboard
            ]);

            // Ambil barang dari session
            $barangSession = session('barang');
            if (!$barangSession || !isset($barangSession['id_barang'])) {
                return redirect()->route('dashboard')->with('error', 'Sesi barang habis. Silakan scan ulang.');
            }
            $id_barang = $barangSession['id_barang'];

            // 1. LOGIKA SIMPAN GAMBAR (Base64 -> File Storage)
            $relativePath = null;
            if ($request->filled('image_data')) {
                // Ambil string base64
                $image = $request->input('image_data');
                // Bersihkan header data:image...
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                // Decode jadi binary
                $imageData = base64_decode($image);

                // Buat nama file unik
                $fileName = 'transaksi_' . time() . '_' . Str::random(5) . '.png';
                
                // Simpan ke folder public/bukti_transaksi menggunakan Storage Facade
                Storage::disk('public')->put('bukti_transaksi/' . $fileName, $imageData);
                
                // Simpan path relatif ke database
                $relativePath = 'bukti_transaksi/' . $fileName;
            }

            // 2. LOGIKA STOK GUDANG
            $gudang = Gudang::where('id_barang', $id_barang)->first();
            
            // Auto create jika belum ada (jaga-jaga)
            if (!$gudang) {
                $gudang = Gudang::create([
                    'id_barang' => $id_barang,
                    'nama_barang' => $barangSession['nama_barang'],
                    'jenis_barang' => $barangSession['jenis_barang'],
                    'stok' => 0,
                    'lokasi_rak' => $request->lokasi_rak ?? '-'
                ]);
            }

            if ($request->proses === 'masuk') {
                $gudang->increment('stok', $request->kuantitas);
                if ($request->filled('lokasi_rak')) {
                    $gudang->update(['lokasi_rak' => $request->lokasi_rak]);
                }
            } else {
                // Cek Stok
                if ($gudang->stok < $request->kuantitas) {
                    return redirect()->back()->with('error', 'Stok tidak mencukupi. Sisa: ' . $gudang->stok);
                }
                $gudang->decrement('stok', $request->kuantitas);
            }

            // 3. SIMPAN RIWAYAT TRANSAKSI
            Transaksi::create([
                'id_barang' => $id_barang,
                'tipe_transaksi' => $request->proses,
                'kuantitas' => $request->kuantitas,
                'nama_pengirim_penerima' => strtoupper($request->nama_pengirim_penerima),
                'waktu' => now(),
                'catatan' => $request->catatan,
                'photo' => $relativePath, // Path yang disimpan bersih (bukti_transaksi/file.png)
            ]);

            // Hapus session agar dashboard kembali ke mode scan
            session()->forget('barang');

            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil disimpan!');

        } catch (\Exception $e) {
            Log::error('Error Transaksi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem.');
        }
    }

    public function export(Request $request)
    {
        try {
            $allData = Transaksi::with('barang')->orderBy('waktu', 'desc')->get();
            $filteredData = collect($request->input('filteredData', []));

            return Excel::download(new TransaksiExport($allData, $filteredData), 'transaksi.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal ekspor: ' . $e->getMessage());
        }
    }
}