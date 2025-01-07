<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\BarangController;

// Halaman Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::post('/proses', [DashboardController::class, 'process'])->name('dashboard.process');

// Halaman Gudang
Route::get('/gudang', [GudangController::class, 'index'])->name('gudang.index');

// Halaman Transaksi
Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store');
 
Route::get('/fetch-barang', [DashboardController::class, 'fetchBarang'])->name('fetch.barang');

// Rute untuk halaman barang
Route::get('/barang/create', [BarangController::class, 'create'])->name('barang.create');
Route::post('/barang/store', [BarangController::class, 'store'])->name('barang.store');
