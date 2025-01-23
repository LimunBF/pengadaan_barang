<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\Transaksi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\AuthService; // Tambahkan import AuthService

class TransaksiController extends Controller
{
    public function __construct()
    {
        AuthService::checkLogin(); // Panggil pengecekan login
    }

    public function index()
    {
        $transaksi = Transaksi::all();
        $transaksi = Transaksi::with('barang')->get();
        $tanggalPertama = Transaksi::orderBy('waktu', 'asc')->value('waktu');
        $tanggalTerakhir = Transaksi::orderBy('waktu', 'desc')->value('waktu');
    
        // Format tanggal menjadi YYYY-MM-DD
        $tanggalPertama = $tanggalPertama ? date('Y-m-d', strtotime($tanggalPertama)) : null;
        $tanggalTerakhir = $tanggalTerakhir ? date('Y-m-d', strtotime($tanggalTerakhir)) : null;
    
        return view('transaksi.index', compact('transaksi', 'tanggalPertama', 'tanggalTerakhir'));
    }
    
    public function masuk()
    {
        $transaksi = Transaksi::where('tipe_transaksi', 'masuk')->get();
        return view('transaksi.masuk', compact('transaksi'));
    }

    public function keluar()
    {
        $transaksi = Transaksi::where('tipe_transaksi', 'keluar')->get();
        return view('transaksi.keluar', compact('transaksi'));
    }

    public function store(Request $request)
    {
        Log::info('Data yang diterima di request:', $request->all()); // Debug data input
        Log::info('Data yang diterima:', $request->all());
        Log::info('Data kuantitas:', ['kuantitas' => $request->input('kuantitas')]);        
        try {
            // Validasi input manual
            $request->validate([
                'proses' => 'required|in:masuk,keluar',
                'kuantitas' => 'required|integer|min:1',
                'nama_pengirim_penerima' => 'required|string|max:255',
                'catatan' => 'nullable|string',
                'image_data' => 'nullable|string', // Validasi untuk foto
            ]);
    
            // Ambil data barang dari session
            $barang = session('barang');
            if (!$barang) {
                return redirect()->back()->with('error', 'Data barang tidak ditemukan di session.');
            }
    
            // Pastikan session barang memiliki semua data yang diperlukan
            if (!isset($barang['id_barang'], $barang['nama_barang'], $barang['jenis_barang'])) {
                return redirect()->back()->with('error', 'Data barang di session tidak lengkap.');
            }
    
            // Data dari session
            $id_barang = $barang['id_barang'];
            $nama_barang = $barang['nama_barang'];
            $jenis_barang = $barang['jenis_barang'];
    
            // Data dari form
            $proses = $request->input('proses');
            $kuantitas = $request->input('kuantitas');
            $lokasi_rak = $request->input('lokasi_rak');
            $nama_pengirim_penerima = strtoupper($request->input('nama_pengirim_penerima'));
            $catatan = $request->input('catatan');
            $imageData = $request->input('image_data');
    
            // Proses penyimpanan gambar (jika ada)
            $photoUrl = null;
            if ($imageData) {
                // Buat format tanggal dan waktu
                $currentDateTime = now()->format('d-m-Y(H_i_s)');
                $fileName = 'transaksi_' . $currentDateTime . '.png';

                // Decode base64 gambar
                $image = str_replace('data:image/png;base64,', '', $imageData);
                $image = str_replace(' ', '+', $image);
                $image = base64_decode($image);

                // Path penyimpanan di storage/app/public/foto_bukti
                $directoryPath = storage_path('app/public/foto_bukti');
                if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0777, true); // Buat folder jika belum ada
                }

                $filePath = $directoryPath . '/' . $fileName;
                file_put_contents($filePath, $image);

                // URL publik file (path ke public/storage)
                $photoUrl = asset('storage/foto_bukti/' . $fileName);
            }

    
            // Proses transaksi
            $gudang = Gudang::where('id_barang', $id_barang)->first();
            if ($proses === 'masuk') {
                if ($gudang) {
                    $gudang->stok += $kuantitas;
                    $gudang->save();
                } else {
                    Gudang::create([
                        'id_barang' => $id_barang,
                        'nama_barang' => $nama_barang,
                        'jenis_barang' => $jenis_barang,
                        'lokasi_rak' => $lokasi_rak,
                        'stok' => $kuantitas,
                    ]);
                }
            } elseif ($proses === 'keluar') {
                if ($gudang) {
                    if ($gudang->stok < $kuantitas) {
                        return redirect()->back()->with('error', 'Stok barang tidak mencukupi.');
                    }
                    $gudang->stok -= $kuantitas;
                    $gudang->save();
                } else {
                    return redirect()->back()->with('error', 'Barang tidak ditemukan di gudang.');
                }
            }
    
            // Simpan transaksi
            Transaksi::create([
                'id_barang' => $id_barang,
                'tipe_transaksi' => $proses,
                'kuantitas' => $kuantitas,
                'nama_pengirim_penerima' => $nama_pengirim_penerima,
                'waktu' => now(),
                'catatan' => $catatan,
                'photo' => $photoUrl,
            ]);
    
            return redirect()->back()->with('success', 'Transaksi berhasil dicatat!');
        } catch (\Exception $e) {
            Log::error('Error pada transaksi store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan transaksi.');
        }
    }  
}
