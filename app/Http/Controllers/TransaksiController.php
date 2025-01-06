<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Simulasi data transaksi
        $transaksis = [
            [
                'id_transaksi' => 1,
                'id_barang' => 'B001',
                'nama_barang' => 'Laptop',
                'tipe_transaksi' => 'masuk',
                'kuantitas' => 2,
                'nama_pengirim_penerima' => 'John Doe',
                'waktu' => '2025-01-06 10:00:00',
                'catatan' => 'Barang baru diterima.',
            ],
            [
                'id_transaksi' => 2,
                'id_barang' => 'B002',
                'nama_barang' => 'Printer',
                'tipe_transaksi' => 'keluar',
                'kuantitas' => 1,
                'nama_pengirim_penerima' => 'Jane Doe',
                'waktu' => '2025-01-06 11:00:00',
                'catatan' => 'Barang dikirim ke divisi IT.',
            ],
        ];

        return view('transaksi.index', compact('transaksis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('transaksi.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'id_barang' => 'required|string|max:50',
            'tipe_transaksi' => 'required|in:masuk,keluar',
            'kuantitas' => 'required|integer|min:1',
            'nama_pengirim_penerima' => 'required|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        // Simulasi menyimpan data
        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dicatat.');
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
