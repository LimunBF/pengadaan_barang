<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGudangTable extends Migration
{
    public function up()
    {
        Schema::create('gudang', function (Blueprint $table) {
            $table->string('id_barang', 50)->primary();
            $table->string('nama_barang', 255);
            $table->string('jenis_barang', 255);
            $table->string('lokasi_rak', 255);
            $table->text('deskripsi_barang')->nullable();
            $table->integer('stok')->default(0);
            $table->integer('satuan')->nullable();
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gudang');
    }
}
