<?php

namespace App\Http\Controllers;

use App\Models\Petugas;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    // Menampilkan semua petugas
    public function index()
    {
        $petugas = Petugas::all();
        return response()->json($petugas);
    }

    // Menambah petugas baru
    public function store(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $petugas = Petugas::create(['nama' => $request->nama]);
        return response()->json(['success' => 'Petugas berhasil ditambahkan!', 'data' => $petugas]);
    }

    // Mengupdate petugas
    public function update(Request $request, $id)
    {
        $request->validate(['nama' => 'required|string|max:255']);
        $petugas = Petugas::findOrFail($id);
        $petugas->update(['nama' => $request->nama]);
        return response()->json(['success' => 'Petugas berhasil diperbarui!', 'data' => $petugas]);
    }

    // Menghapus petugas
    public function destroy($id)
    {
        $petugas = Petugas::findOrFail($id);
        $petugas->delete();
        return response()->json(['success' => 'Petugas berhasil dihapus!']);
    }
}
