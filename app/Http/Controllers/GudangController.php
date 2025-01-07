<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GudangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Simulasi data barang di gudang
        $gudangs = [
            [
                'id_barang' => 'B001',
                'nama_barang' => 'Kunci',
                'jenis_barang' => 'Elektronik',
                'deskripsi_barang' => 'Laptop untuk kantor.',
                'stok' => 10,
            ],
            [
                'id_barang' => 'B002',
                'nama_barang' => 'Printer',
                'jenis_barang' => 'Elektronik',
                'deskripsi_barang' => 'Printer multifungsi.',
                'stok' => 5,
            ],
        ];

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
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
            'deskripsi_barang' => 'nullable|string',
            'stok' => 'required|integer|min:0',
        ]);

        // Simulasi menyimpan data
        return redirect()->route('gudang.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
