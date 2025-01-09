<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;

    protected $table = 'gudang'; // Nama tabel
    protected $primaryKey = 'id_barang'; // Primary key
    public $incrementing = false; // Jika primary key bukan auto-increment
    protected $keyType = 'string'; // Tipe data primary key

    // Kolom yang dapat diisi
    protected $fillable = [
        'id_barang',
        'nama_barang',
        'jenis_barang',
        'lokasi_rak',
        'deskripsi_barang',
        'stok',
        'satuan',
        'created_at',
        'updated_at',
    ];
}
