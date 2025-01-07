<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangTable extends Migration
{
    public function up()
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->string('id_barang', 50)->primary();
            $table->string('nama_barang', 255);
            $table->string('jenis_barang', 255);
            $table->text('deskripsi_barang')->nullable();
            $table->text('kode_qr')->nullable(); // Menyimpan JSON data
            $table->string('qr_code_path', 255)->nullable(); // Path untuk gambar QR Code
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('barang');
    }
}
