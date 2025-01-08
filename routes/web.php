<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DaftarBarangController;
use App\Http\Controllers\BarcodeController;

// Halaman Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/proses', [DashboardController::class, 'process'])->name('dashboard.process');

// Halaman Gudang
Route::get('/gudang', [GudangController::class, 'index'])->name('gudang.index');

// Halaman Transaksi
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store');
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');

 
Route::get('/fetch-barang', [DashboardController::class, 'fetchBarang'])->name('fetch.barang');

// Rute untuk halaman barang
Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
Route::post('/barang/store', [BarangController::class, 'store'])->name('barang.store');
Route::get('/barang/{id_barang}/download-qr', [BarangController::class, 'downloadQRCode'])->name('barang.download_qr');
Route::get('/barang/{id}', [BarangController::class, 'getBarang'])->name('barang.get');
Route::get('/barang/{id_barang}', [BarangController::class, 'show'])->name('barang.show');
Route::post('/scan-barcode', [BarangController::class, 'scan']);

//Rute untuk halaman daftar barang
Route::get('/barang', [DaftarBarangController::class, 'index'])->name('barang.index');
Route::get('/barang/{id_barang}/download', [DaftarBarangController::class, 'downloadQRCode'])->name('barang.downloadQRCode');









