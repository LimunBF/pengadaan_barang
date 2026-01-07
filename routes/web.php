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
use App\Http\Controllers\PetugasController;


// Halaman Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/dashboard/clear', [DashboardController::class, 'clearSession'])->name('dashboard.clear');
Route::post('/proses', [DashboardController::class, 'process'])->name('dashboard.process');
Route::get('/fetch-barang', [DashboardController::class, 'fetchBarang'])->name('fetch.barang');
Route::get('/barcode', function () {
    return view('barcode.barcode-data'); // Nama view harus sesuai dengan lokasi file
})->name('barcode.index');

// Halaman Gudang
Route::resource('gudang', GudangController::class);
Route::get('/gudang/{id}/stok', [GudangController::class, 'show']);
Route::get('/gudang/{id}/stok', [GudangController::class, 'show'])->name('gudang.stok');
Route::put('/gudang/{id}', [GudangController::class, 'update'])->name('gudang.update');

// Rute Untuk Fungsi CRUD Petugas
Route::get('/petugas', [PetugasController::class, 'index'])->name('petugas.index');
Route::post('/petugas', [PetugasController::class, 'store'])->name('petugas.store');
Route::put('/petugas/{id}', [PetugasController::class, 'update'])->name('petugas.update');
Route::delete('/petugas/{id}', [PetugasController::class, 'destroy'])->name('petugas.destroy');

// Halaman Transaksi
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store'); // Hanya satu rute POST
Route::get('/transaksi/masuk', [TransaksiController::class, 'masuk'])->name('transaksi.masuk');
Route::get('/transaksi/keluar', [TransaksiController::class, 'keluar'])->name('transaksi.keluar');
// Untuk Export Excel Pada Transaksi
Route::post('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');
// Route::get('/transaksi/export', [TransaksiController::class, 'export'])->name('transaksi.export');

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


//route export excel
Route::get('/export-excel', [GudangController::class,'exportGudang']);
Route::get('/export-gudang', [GudangController::class, 'exportGudang']);
Route::get('/gudang/export', [GudangController::class, 'export'])->name('gudang.export');
