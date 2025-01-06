<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\TransaksiController;

// Halaman Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Resource Routes
Route::resource('gudang', GudangController::class);
Route::resource('transaksi', TransaksiController::class);