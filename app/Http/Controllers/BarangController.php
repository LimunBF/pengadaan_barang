<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang; // <--- PASTIKAN INI ADA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    public function index()
    {
        $barang = Barang::all();
        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        do {
            $nextId = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Barang::where('id_barang', $nextId)->exists()); 
    
        return view('barang.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'id_barang' => 'required|string|max:50|unique:barang',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'nullable|string|max:255',
            'foto_barang' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $jenis_barang = $request->jenis_barang ?: '(kosong)';

        // 2. Simpan Foto Barang
        $fotoPath = $request->file('foto_barang')->store('barang_photos', 'public');
        $fotoUrl = url('storage/' . $fotoPath);

        // 3. Simpan Data Barang (Master Data)
        $barang = Barang::create([
            'id_barang' => $request->id_barang,
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $jenis_barang,
            'foto_barang' => $fotoUrl,
            'kondisi' => 'ada',
        ]);

        // --- FITUR BARU: OTOMATIS DAFTAR KE GUDANG ---
        // Saat barang dibuat, daftarkan ke gudang dengan stok 0
        Gudang::create([
            'id_barang' => $barang->id_barang,
            'nama_barang' => $barang->nama_barang,
            'jenis_barang' => $jenis_barang,
            'stok' => 0,           // Stok awal 0
            'lokasi_rak' => '-',   // Lokasi default
        ]);
        // ---------------------------------------------

        // 4. Generate QR Code (Nama File Aman vs Label Cantik)
        $namaFileAman = Str::limit(Str::slug($barang->nama_barang), 50, '') . '-' . $barang->id_barang . '.png';
        $labelGambar = $barang->nama_barang;

        $relativePath = $this->qrCodeService->generateQRCode(
            json_encode(['id_barang' => $barang->id_barang]), 
            $namaFileAman, 
            $labelGambar   
        );

        // 5. Update Database Barang dengan path QR
        $barang->update([
            'kode_qr' => json_encode([
                'id_barang' => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'jenis_barang' => $jenis_barang,
            ]),
            'qr_code_path' => $relativePath,
        ]);

        $publicQrUrl = url('storage/' . $relativePath);

        session([
            'last_generated_id' => $barang->id_barang,
            'qr_code_url' => $publicQrUrl,
        ]);

        return redirect()
            ->route('barang.create')
            ->with('success', 'Barang berhasil ditambahkan dan terdaftar di Gudang (Stok 0).')
            ->with('qr_code_url', $publicQrUrl);
    }

    public function downloadQRCode($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();

        if (empty($barang->qr_code_path)) {
            return back()->with('error', 'Data QR Code belum digenerate.');
        }

        $filePath = storage_path("app/public/" . $barang->qr_code_path);

        if (!file_exists($filePath)) {
            Log::error("File QR tidak ditemukan di: {$filePath}");
            return back()->with('error', 'File fisik QR Code tidak ditemukan di server.');
        }

        $downloadName = Str::slug($barang->nama_barang) . '-' . $barang->id_barang . '.png';

        return response()->download($filePath, $downloadName);
    }

    public function getBarang($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->where('kondisi', 'ada')->first();
        if (!$barang) return response()->json(['error' => 'Data barang tidak ditemukan'], 404);
        return response()->json($barang);
    }
    
    public function scan(Request $request)
    {
        $jsonData = $request->input('barcode_data'); 
        $barang = Barang::where('kode_qr', $jsonData)->first();
        if (!$barang) return abort(404, 'Barang tidak ditemukan.');
        $decodedData = json_decode($barang->kode_qr, true);
        return view('barcode-scan-result', compact('decodedData'));
    }

    public function show($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->where('kondisi', 'ada')->firstOrFail();
        return view('barang-detail', compact('barang'));
    }
    
    public function update(Request $request, $id_barang)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
        ]);
        $barang = Barang::findOrFail($id_barang);
        $barang->update([
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
        ]);
        
        // Update juga nama di Gudang agar sinkron
        Gudang::where('id_barang', $id_barang)->update([
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
        ]);
        
        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }
}