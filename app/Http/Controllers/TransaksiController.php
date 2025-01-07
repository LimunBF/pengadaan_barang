<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\Gudang;

class TransaksiController extends Controller
{
    public function index()
    {
        // Ambil semua data transaksi
        $transaksi = Transaksi::orderBy('waktu', 'desc')->get();

        // Return view dengan data transaksi
        return view('transaksi.index', compact('transaksi'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'proses' => 'required|in:masuk,keluar',
            'id_barang' => 'required|string|exists:gudang,id_barang',
            'kuantitas' => 'required|integer|min:1',
            'nama_pengirim_penerima' => 'required|string|max:255',
            'catatan' => 'nullable|string',
        ]);
        $request->validate([
            'proses' => 'required|in:masuk,keluar',
            'id_barang' => 'required|string|exists:gudang,id_barang',
            'kuantitas' => 'required|integer|min:1',
            'nama_pengirim_penerima' => 'required|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        // Ambil data barang dari tabel gudang
        $barang = Gudang::where('id_barang', $request->id_barang)->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan.');
        }

        // Update stok berdasarkan jenis transaksi
        if ($request->proses === 'masuk') {
            $barang->stok += $request->kuantitas;
        } elseif ($request->proses === 'keluar') {
            if ($barang->stok < $request->kuantitas) {
                return redirect()->back()->with('error', 'Stok barang tidak mencukupi.');
            }
            $barang->stok -= $request->kuantitas;
        }

        // Simpan perubahan stok barang
        $barang->save();

        // Simpan transaksi ke database
        Transaksi::create([
            'id_barang' => $request->id_barang,
            'tipe_transaksi' => $request->proses,
            'kuantitas' => $request->kuantitas,
            'nama_pengirim_penerima' => $request->nama_pengirim_penerima,
            'waktu' => now(),
            'catatan' => $request->catatan,
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil disimpan.');

        // Simpan logika store seperti sebelumnya
    }
}
