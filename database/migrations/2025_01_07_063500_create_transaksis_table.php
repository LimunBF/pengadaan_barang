<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('id_barang');
            $table->enum('tipe_transaksi', ['masuk', 'keluar']);
            $table->integer('kuantitas');
            $table->string('nama_pengirim_penerima');
            $table->timestamp('waktu')->default(now());
            $table->text('catatan')->nullable();
            $table->timestamps(); // Menambahkan created_at dan updated_at secara otomatis

            $table->foreign('id_barang')->references('id_barang')->on('gudang')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
}
