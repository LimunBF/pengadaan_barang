<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Illuminate\Support\Str; 
use App\Services\QRCodeService;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    private $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    public function create()
    {
        // Generate ID acak 4 digit yang belum terpakai
        do {
            $nextId = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Barang::where('id_barang', $nextId)->exists()); 
    
        return view('barang.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|string|unique:barang,id_barang',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'nullable|string',
            'foto_barang' => 'required|image|max:2048',
        ]);

        // 1. Simpan Gambar (Path Relative)
        $fotoPath = $request->file('foto_barang')->store('barang_photos', 'public');

        // 2. Simpan Master Barang
        $barang = Barang::create([
            'id_barang' => $request->id_barang,
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang ?? '-',
            'foto_barang' => $fotoPath, // Simpan path saja: barang_photos/xxx.jpg
            'kondisi' => 'ada',
        ]);

        // 3. Masukkan ke Gudang (Stok Awal 0)
        Gudang::create([
            'id_barang' => $barang->id_barang,
            'nama_barang' => $barang->nama_barang,
            'jenis_barang' => $barang->jenis_barang,
            'stok' => 0,
            'lokasi_rak' => '-',
        ]);

        // 4. Generate QR Code
        $namaFileAman = Str::slug($barang->nama_barang) . '-' . $barang->id_barang . '.png';
        $qrPath = $this->qrCodeService->generateQRCode(
            json_encode(['id_barang' => $barang->id_barang]), 
            $namaFileAman, 
            $barang->nama_barang
        );

        $barang->update(['qr_code_path' => $qrPath]);

        // Redirect dengan session flash untuk QR
        return redirect()->route('barang.create')
            ->with('success', 'Barang berhasil didaftarkan!')
            ->with('last_generated_id', $barang->id_barang)
            ->with('qr_code_url', asset('storage/' . $qrPath)); // Kirim URL lengkap untuk view
    }
}