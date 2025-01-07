<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    public $incrementing = true;

    protected $fillable = [
        'id_barang',
        'tipe_transaksi',
        'kuantitas',
        'nama_pengirim_penerima',
        'waktu',
        'catatan',
    ];
}
