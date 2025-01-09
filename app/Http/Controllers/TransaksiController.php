<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang;
use App\Models\Transaksi;
use Illuminate\Support\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        // Ambil semua data transaksi dari database
        $transaksi = Transaksi::all();

        // Kirim data transaksi ke view
        return view('transaksi.index', compact('transaksi'));
    }
    public function keluar()
    {
        // Ambil semua data transaksi dari database
        $transaksi = Transaksi::all();

        // Kirim data transaksi ke view
        return view('transaksi.index', compact('transaksi'));
    }
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'proses' => 'required|in:masuk,keluar',
            'id_barang' => 'required|string|max:50',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'deskripsi_barang' => 'nullable|string',
            'kuantitas' => 'required|integer|min:1',
            'nama_pengirim_penerima' => 'required|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        // Ambil input dari form
        $id_barang = $request->input('id_barang');
        $nama_barang = $request->input('nama_barang');
        $jenis_barang = $request->input('jenis_barang');
        $deskripsi_barang = $request->input('deskripsi_barang');
        $kuantitas = $request->input('kuantitas');
        $nama_pengirim_penerima = $request->input('nama_pengirim_penerima');
        $catatan = $request->input('catatan');
        $proses = $request->input('proses'); // masuk atau keluar

        // Cek apakah barang sudah ada di tabel gudang
        $gudang = Gudang::where('id_barang', $id_barang)->first();

        // Proses Barang Masuk
        if ($proses === 'masuk') {
            if ($gudang) {
                // Jika barang sudah ada, tambahkan stok
                $gudang->stok += $kuantitas;
                $gudang->save();
            } else {
                // Jika barang belum ada, tambahkan ke tabel gudang
                Gudang::create([
                    'id_barang' => $id_barang,
                    'nama_barang' => $nama_barang,
                    'jenis_barang' => $jenis_barang,
                    'deskripsi_barang' => $deskripsi_barang,
                    'stok' => $kuantitas,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Proses Barang Keluar
        if ($proses === 'keluar') {
            if ($gudang) {
                // Pastikan stok cukup untuk dikurangi
                if ($gudang->stok < $kuantitas) {
                    return redirect()->back()->with('error', 'Stok barang tidak mencukupi.');
                }
                $gudang->stok -= $kuantitas;
                $gudang->save();
            } else {
                return redirect()->back()->with('error', 'Barang tidak ditemukan di gudang.');
            }
        }

        // Catat transaksi di tabel transaksi
        Transaksi::create([
            'id_barang' => $id_barang,
            'tipe_transaksi' => $proses,
            'kuantitas' => $kuantitas,
            'nama_pengirim_penerima' => $nama_pengirim_penerima,
            'waktu' => now(),
            'catatan' => $catatan,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dicatat.');
    }
}


