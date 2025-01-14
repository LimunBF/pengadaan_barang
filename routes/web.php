<?php
use App\Models\Gudang;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DaftarBarangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\PhotoController;

// Halaman Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/proses', [DashboardController::class, 'process'])->name('dashboard.process');

// Halaman Gudang
Route::get('/gudang', [GudangController::class, 'index'])->name('gudang.index');
Route::get('/gudang/{id}', [GudangController::class, 'show'])->name('gudang.show');
Route::resource('gudang', GudangController::class);
Route::get('/gudang/{id}/stok', [GudangController::class, 'show']);


// Halaman Transaksi
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store');
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::get('/transaksi/masuk', [TransaksiController::class, 'masuk'])->name('transaksi.masuk');
Route::get('/transaksi/keluar', [TransaksiController::class, 'keluar'])->name('transaksi.keluar');


Route::get('/fetch-barang', [DashboardController::class, 'fetchBarang'])->name('fetch.barang');

// Rute untuk halaman barang
Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
Route::post('/barang/store', [BarangController::class, 'store'])->name('barang.store');
Route::get('/barang/{id_barang}/download-qr', [BarangController::class, 'downloadQRCode'])->name('barang.download_qr');
Route::get('/barang/{id}', [BarangController::class, 'getBarang'])->name('barang.get');
Route::get('/barang/{id_barang}', [BarangController::class, 'show'])->name('barang.show');
Route::post('/scan-barcode', [BarangController::class, 'scan']);
Route::put('/barang/{id_barang}', [BarangController::class, 'update'])->name('barang.update');

// R U T E   U N T U K   H A L A M A N  D A F T A R  B A R A N G
// Rute untuk halaman daftar barang
Route::get('/barang', [DaftarBarangController::class, 'index'])->name('barang.index');
// Rute untuk mendownload QR Code
Route::get('/barang/{id_barang}/download', [DaftarBarangController::class, 'downloadQRCode'])->name('barang.downloadQRCode');
// Rute untuk mengupdate data barang
Route::put('/barang/{id_barang}', [DaftarBarangController::class, 'update'])->name('barang.update');
// Rute untuk menghapus barang
Route::delete('/barang/{id_barang}', [DaftarBarangController::class, 'destroy'])->name('barang.destroy');
// Rute untuk mengaktifkan mode edit
Route::post('/barang/{id}/edit-mode', [DaftarBarangController::class, 'enableEditMode'])->name('barang.edit_mode');
// Rute untuk Menghapus Session edit_id
Route::post('/barang/{id}/cancel-edit', [DaftarBarangController::class, 'cancelEdit'])->name('barang.cancel_edit');


//LOGIN LOGOUT RUTE
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login'); // Route GET untuk halaman login
Route::post('/login', [AuthController::class, 'login'])->name('login.post'); // Route POST untuk memproses login
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Rute Untuk Barcode Scanner
Route::get('/barcode-data', function () {
    return view('barcode.barcode-data');
})->name('barcode.data');


Route::post('/proses-scan', [ScanController::class, 'processScan'])->name('proses.scan');
Route::post('/photo/store', [PhotoController::class, 'store'])->name('photo.store');
Route::post('/transaksi/store', [PhotoController::class, 'store'])->name('transaksi.store');
