<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang benar
    protected $table = 'barang';

    // Tentukan primary key (opsional jika tidak menggunakan `id`)
    protected $primaryKey = 'id_barang';

    // Nonaktifkan auto-increment untuk primary key karena menggunakan string
    public $incrementing = false;

    // Tentukan tipe primary key
    protected $keyType = 'string';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'id_barang',
        'nama_barang',
        'jenis_barang',
        'deskripsi_barang',
        'kode_qr',
        'qr_code_path',
        'foto_barang', // Path untuk foto barang
        'kondisi',     // Kolom untuk status barang ('ada' atau 'dihapus')
    ];

    // Scope untuk barang yang masih aktif (kondisi 'ada')
    public function scopeAktif($query)
    {
        return $query->where('kondisi', 'ada');
    }

    // Scope untuk barang yang dihapus (kondisi 'dihapus')
    public function scopeDihapus($query)
    {
        return $query->where('kondisi', 'dihapus');
    }

    // Custom accessor untuk kondisi (opsional jika ingin memberikan deskripsi lebih)
    public function getKondisiLabelAttribute()
    {
        return $this->kondisi === 'ada' ? 'Tersedia' : 'Telah Dihapus';
    }
}
