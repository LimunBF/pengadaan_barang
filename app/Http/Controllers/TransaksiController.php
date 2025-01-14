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
        $transaksi = Transaksi::all();
        return view('transaksi.index', compact('transaksi'));
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
        $request->validate([
            'proses' => 'required|in:masuk,keluar',
            'id_barang' => 'required|string|max:50',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'lokasi_rak' => 'required|string|max:255',
            'deskripsi_barang' => 'nullable|string',
            'kuantitas' => 'required|integer|min:1',
            'nama_pengirim_penerima' => 'required|string|max:255',
            'catatan' => 'nullable|string',
            'image' => 'nullable|string', // Menambahkan validasi untuk foto
        ]);

        $id_barang = $request->input('id_barang');
        $nama_barang = $request->input('nama_barang');
        $jenis_barang = $request->input('jenis_barang');
        $lokasi_rak = $request->input('lokasi_rak');
        $deskripsi_barang = $request->input('deskripsi_barang');
        $kuantitas = $request->input('kuantitas');
        $nama_pengirim_penerima = $request->input('nama_pengirim_penerima');
        $catatan = $request->input('catatan');
        $proses = $request->input('proses');
        $imageData = $request->input('image'); // Foto dalam base64

        // Simpan foto jika ada
        $photoUrl = null;
        if ($imageData) {
            $fileName = 'photo_' . time() . '.png';
            $image = str_replace('data:image/png;base64,', '', $imageData);
            $image = str_replace(' ', '+', $image);
            $image = base64_decode($image);

            $localPath = public_path('photos/' . $fileName);

            if (!file_exists(public_path('photos'))) {
                mkdir(public_path('photos'), 0777, true);
            }

            file_put_contents($localPath, $image);
            $photoUrl = asset('photos/' . $fileName); // URL foto
        }

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
                    'deskripsi_barang' => $deskripsi_barang,
                    'stok' => $kuantitas,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if ($proses === 'keluar') {
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

        Transaksi::create([
            'id_barang' => $id_barang,
            'tipe_transaksi' => $proses,
            'kuantitas' => $kuantitas,
            'nama_pengirim_penerima' => $nama_pengirim_penerima,
            'waktu' => now(),
            'catatan' => $catatan,
            'photo' => $photoUrl, // Menyimpan URL foto
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dicatat.');
    }
}
