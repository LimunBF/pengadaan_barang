<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan ini

class CreateTransaksiTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('id_barang', 50);
            $table->enum('tipe_transaksi', ['masuk', 'keluar']);
            $table->integer('kuantitas');
            $table->string('nama_pengirim_penerima', 255);
            $table->timestamp('waktu')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('catatan')->nullable();
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
}
