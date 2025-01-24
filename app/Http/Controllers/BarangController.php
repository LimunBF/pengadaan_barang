<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gudang;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;
use Illuminate\Support\Facades\Log;
use App\Services\QRCodeService;
use App\Services\AuthService; // Tambahkan import AuthService

class BarangController extends Controller
{
    public function index()
    {
        // Ambil semua data barang dari database
        $barang = Barang::all();
        // Kirim data ke view index.blade.php
        return view('barang.index', compact('barang'));
    }

    public function create()
    {
        do {
            // Generate angka acak 4 digit
            $nextId = str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Barang::where('id_barang', $nextId)->exists()); // Periksa apakah ID sudah ada di database
    
        // Kirim data ke view create.blade.php
        return view('barang.create', compact('nextId'));
    }    

    private $qrCodeService;

    public function __construct(QRCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
        AuthService::checkLogin(); // Panggil pengecekan login
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|string|max:50|unique:barang',
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'nullable|string|max:255',
            'foto_barang' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $jenis_barang = $request->jenis_barang ?: '(kosong)';

        // Simpan foto barang
        $fotoPath = $request->file('foto_barang')->store('barang_photos', 'public');
        $fotoUrl = url('storage/' . $fotoPath);

        // Simpan data barang
        $barang = Barang::create([
            'id_barang' => $request->id_barang,
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $jenis_barang,
            'foto_barang' => $fotoUrl,
            'kondisi' => 'ada',
        ]);

        // Generate QR Code
        $qrCodePath = $this->qrCodeService->generateQRCode(
            json_encode(['id_barang' => $barang->id_barang]),
            $barang->id_barang,
            $barang->nama_barang
        );

        // Simpan path QR Code ke database
        $barang->update([
            'kode_qr' => json_encode([
                'id_barang' => $barang->id_barang,
                'nama_barang' => $barang->nama_barang,
                'jenis_barang' => $jenis_barang,
            ]),
            'qr_code_path' => $qrCodePath,
        ]);

        session([
            'last_generated_id' => $barang->id_barang,
            'qr_code_url' => $qrCodePath,
        ]);

        return redirect()
            ->route('barang.create')
            ->with('success', 'Barang berhasil ditambahkan.')
            ->with('qr_code_url', $qrCodePath);
    }

    public function getBarang($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->where('kondisi', 'ada')->first();

        // Jika data tidak ditemukan, kembalikan pesan error
        if (!$barang) {
            return response()->json(['error' => 'Data barang tidak ditemukan atau telah dihapus'], 404);
        }

        // Kembalikan data barang dalam format JSON
        return response()->json($barang);
    }

    public function downloadQRCode($id_barang)
    {
        $barang = Barang::where('id_barang', $id_barang)->firstOrFail();
        $filePath = storage_path("app/public/barang_qr_codes/{$id_barang}.png");

        Log::info("Checking file path: {$filePath}");

        if (!file_exists($filePath)) {
            Log::error("File not found: {$filePath}");
            return redirect()->back()->with('error', 'File QR Code tidak ditemukan.');
        }

        Log::info("File found, proceeding to download: {$filePath}");

        return response()->download($filePath, "{$id_barang}_qrcode.png")->deleteFileAfterSend(false);
    }

    public function scan(Request $request)
    {
        // Ambil data JSON dari barcode yang di-scan
        $jsonData = $request->input('barcode_data'); // Data JSON dari scanner

        // Cari data barang berdasarkan kode_qr di database
        $barang = Barang::where('kode_qr', $jsonData)->first();

        if (!$barang) {
            return abort(404, 'Barang tidak ditemukan.');
        }

        // Decode data JSON dari kolom kode_qr
        $decodedData = json_decode($barang->kode_qr, true);

        // Kirim data ke view untuk ditampilkan
        return view('barcode-scan-result', compact('decodedData'));
    }

    public function show($id_barang)
    {
        // Ambil data barang berdasarkan ID
        $barang = Barang::where('id_barang', $id_barang)->where('kondisi', 'ada')->firstOrFail();

        // Kirim data barang ke view
        return view('barang-detail', compact('barang'));
    }
    public function update(Request $request, $id_barang)
    {
        // Validasi input
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jenis_barang' => 'required|string|max:255',
        ]);

        // Cari data barang
        $barang = Barang::findOrFail($id_barang);

        // Update data barang
        $barang->update([
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
        ]);

        // Perbarui juga data di tabel gudang jika barang terkait ada di sana
        Gudang::where('id_barang', $id_barang)->update([
            'nama_barang' => $request->nama_barang,
            'jenis_barang' => $request->jenis_barang,
        ]);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diperbarui.');
    }
}