<?php

namespace App\Http\Controllers;

use App\Exports\GudangExport;
use Illuminate\Http\Request;
use App\Models\Gudang;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Services\AuthService;
use Illuminate\Support\Facades\Storage;

class GudangController extends Controller
{
    public function __construct()
    {
        // AuthService::checkLogin(); // Aktifkan jika sudah siap
    }

    public function index()
    {
        // Menggunakan with('barang') agar query gambar lebih cepat (Eager Loading)
        $gudangs = Gudang::with('barang')->paginate(15);
        return view('gudang.index', compact('gudangs'));
    }

    public function create()
    {
        return view('gudang.create');
    }

    public function store(Request $request)
    {
        // Validasi lengkap untuk pembuatan baru
        $request->validate([
            'id_barang' => 'required|string|max:50|unique:gudang,id_barang',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'lokasi_rak' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'satuan' => 'nullable|string',
        ]);

        Gudang::create($request->all());

        return redirect()->route('gudang.index')->with('success', 'Barang berhasil ditambahkan ke Gudang.');
    }

    /**
     * UPDATE: Ini bagian yang kita perbaiki total.
     * Kita hanya validasi data yang BISA diedit di form (Stok, Rak, Satuan).
     * ID dan Nama tidak perlu divalidasi ulang karena tidak berubah.
     */
    public function update(Request $request, string $id)
    {
        // 1. Normalisasi ID (misal "1" jadi "0001")
        $id_barang_fixed = str_pad($id, 4, '0', STR_PAD_LEFT);

        // 2. Cari Data
        $gudang = Gudang::where('id_barang', $id_barang_fixed)->firstOrFail();

        // 3. Validasi HANYA input yang ada di Form Edit
        $request->validate([
            'lokasi_rak' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'satuan' => 'nullable|string',
        ]);

        // 4. Update hanya field yang diizinkan
        $gudang->update([
            'lokasi_rak' => $request->lokasi_rak,
            'stok' => $request->stok,
            'satuan' => $request->satuan,
            // nama_barang & jenis_barang tidak diupdate dari sini agar tetap sinkron dengan Master Barang
        ]);

        return redirect()->route('gudang.index')->with('success', 'Data gudang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $id_barang_fixed = str_pad($id, 4, '0', STR_PAD_LEFT);
        $gudang = Gudang::where('id_barang', $id_barang_fixed)->firstOrFail();
        $gudang->delete();

        return redirect()->route('gudang.index')->with('success', 'Data berhasil dihapus dari Gudang.');
    }

    public function exportGudang(Request $request)
    {
        $search = $request->get('search');
        $stokMin = $request->get('stok_min');
        $stokMax = $request->get('stok_max');
        $lokasiRak = $request->get('lokasi_rak');
    
        $query = Gudang::query();

        if ($search) {
            $query->where('nama_barang', 'like', "%$search%");
        }
        if ($stokMin) {
            $query->where('stok', '>=', $stokMin);
        }
        if ($stokMax) {
            $query->where('stok', '<=', $stokMax);
        }
        if ($lokasiRak) {
            $query->where('lokasi_rak', $lokasiRak);
        }

        $gudangs = $query->get();
    
        return Excel::download(new GudangExport($gudangs), 'data-gudang.xlsx');
    }
}