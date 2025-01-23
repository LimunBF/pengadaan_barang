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
        $fotoUrl = asset('storage/' . $fotoPath);

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

     // public function generateQRCode(Barang $barang)
    // {
    //     // Siapkan data untuk QR Code (format JSON)
    //     $qrData = json_encode([
    //         'id_barang' => $barang->id_barang,
    //     ]);

    //     // Path untuk menyimpan QR Code
    //     $qrFolder = 'barang_qr_codes'; // Sub-folder di storage
    //     $qrFileName = "{$barang->id_barang}_raw.png"; // Nama file QR Code
    //     $qrStoragePath = storage_path("app/public/{$qrFolder}"); // Path lengkap di storage

    //     // Buat direktori jika belum ada
    //     if (!is_dir($qrStoragePath)) {
    //         mkdir($qrStoragePath, 0755, true);
    //     }

    //     // Generate QR Code
    //     $qrCode = new QrCode(
    //         data: $qrData,
    //         encoding: new Encoding('UTF-8'),
    //         errorCorrectionLevel: ErrorCorrectionLevel::High,
    //         size: 300,
    //         margin: 10,
    //         roundBlockSizeMode: RoundBlockSizeMode::Margin,
    //         foregroundColor: new Color(0, 0, 0), // Warna hitam
    //         backgroundColor: new Color(255, 255, 255) // Warna putih
    //     );

    //     // Tentukan lokasi file QR Code
    //     $qrImagePath = "{$qrStoragePath}/{$qrFileName}";

    //     // Simpan QR Code sebagai gambar (tanpa teks)
    //     $writer = new PngWriter();
    //     $writer->write($qrCode)->saveToFile($qrImagePath);

    //     // **Tambahkan Teks ke Gambar**
    //     $finalFileName = "{$barang->id_barang}.png"; // Nama file final
    //     $finalImagePath = "{$qrStoragePath}/{$finalFileName}";
    //     $this->addTextToImage($qrImagePath, $finalImagePath, $barang->nama_barang);

    //     // Hapus gambar QR code asli tanpa teks jika tidak diperlukan
    //     unlink($qrImagePath);

    //     // Kembalikan URL path gambar QR Code
    //     return asset("storage/{$qrFolder}/{$finalFileName}");
    // }


    // /**
    //  * Tambahkan teks ke gambar
    //  *
    //  * @param string $sourcePath Path gambar QR code asli
    //  * @param string $destinationPath Path gambar final dengan teks
    //  * @param string $text Teks yang akan ditambahkan
    //  */
    // private function addTextToImage($sourcePath, $destinationPath, $text)
    // {
    //     // Load gambar QR code
    //     $image = imagecreatefrompng($sourcePath);
    
    //     // Tentukan ukuran gambar QR code
    //     $width = imagesx($image);
    //     $height = imagesy($image);
    
    //     // Tambahkan tinggi untuk teks
    //     $newHeight = $height + 70; // Tambahkan 70px untuk teks
    //     $newImage = imagecreatetruecolor($width, $newHeight);
    
    //     // Warna putih untuk latar belakang
    //     $white = imagecolorallocate($newImage, 255, 255, 255);
    //     imagefill($newImage, 0, 0, $white);
    
    //     // Salin gambar QR code ke gambar baru
    //     imagecopy($newImage, $image, 0, 0, 0, 0, $width, $height);
    
    //     // Warna hitam untuk teks
    //     $black = imagecolorallocate($newImage, 0, 0, 0);
    
    //     // Lokasi font TTF
    //     $fontPath = storage_path('fonts/Rubik-Bold.ttf');
    
    //     // Ukuran font
    //     $fontSize = 30; // Ukuran font dalam poin
    
    //     // Hitung posisi teks
    //     $bbox = imagettfbbox($fontSize, 0, $fontPath, $text);
    //     $textWidth = abs($bbox[2] - $bbox[0]);
    //     $xPosition = ($width - $textWidth) / 2; // Teks di tengah
    //     $yPosition = $height + 40; // Teks 40px di bawah gambar
    
    //     // Tambahkan teks ke gambar
    //     imagettftext($newImage, $fontSize, 0, $xPosition, $yPosition, $black, $fontPath, $text);
    
    //     // Simpan gambar baru
    //     imagepng($newImage, $destinationPath);
    
    //     // Hapus sumber daya gambar dari memori
    //     imagedestroy($image);
    //     imagedestroy($newImage);
    // }    