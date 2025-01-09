<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gudang; // Import model Gudang

class GudangController extends Controller
{
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data dari tabel gudang
        $gudangs = Gudang::all();

        // Kirim data ke view
        return view('gudang.index', compact('gudangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('gudang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'id_barang' => 'required|string|max:50|unique:gudang,id_barang',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'lokasi_rak' => 'required|string|max:255',
            'deskripsi_barang' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'satuan' => 'nullable|string',
        ]);

        // Simpan data ke tabel gudang
        Gudang::create($request->all());

        return redirect()->route('gudang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Cari data barang berdasarkan id_barang
        $gudang = Gudang::where('id_barang', $id)->first();
    
        if (!$gudang) {
            return response()->json(['error' => 'Barang tidak ditemukan'], 404);
        }
    
        // Kembalikan data stok sebagai JSON
        return response()->json([
            'stok' => $gudang->stok,
            'nama_barang' => $gudang->nama_barang,
            'jenis_barang' => $gudang->jenis_barang,
            'lokasi_rak' => $gudang->lokasi_rak,
        ]);
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $gudang = Gudang::findOrFail($id);

        return view('gudang.edit', compact('gudang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validasi data
        $request->validate([
          'id_barang' => 'required|string|max:50|unique:gudang,id_barang',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'lokasi_rak' => 'required|string|max:255',
            'deskripsi_barang' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'satuan' => 'nullable|string',
        ]);

        // Update data di tabel gudang
        $gudang = Gudang::findOrFail($id);
        $gudang->update($request->all());

        return redirect()->route('gudang.index')->with('success', 'Barang berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gudang = Gudang::findOrFail($id);
        $gudang->delete();

        return redirect()->route('gudang.index')->with('success', 'Barang berhasil dihapus.');
    }
}
