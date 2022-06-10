<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('data_services', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat');
            $table->string('telepon');
            $table->string('kd_buku');
            $table->string('judul_buku');
            $table->integer('jumlah');
            $table->timestamp('tanggal')->default(now());
            $table->enum('action', ['Pinjam','Kembali']);
            $table->enum('status', ['Meminjam','Tidak Ada Pinjaman','Masih Ada Pinjaman']);
            $table->timestamps();

            // $table->foreign('kd_buku')->references('kode_buku')->on('books');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('data_services');
    }
};
