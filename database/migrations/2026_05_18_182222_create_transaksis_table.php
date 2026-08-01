<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan');
            $table->string('nomor_hp')->nullable();
            $table->float('berat_kg'); // Menggunakan float untuk berat kiloan (misal: 2.5 kg)
            $table->integer('harga_per_kg')->default(6000); // Harga default per kilo
            $table->integer('total_harga');
            $table->enum('status_laundry', ['antrean', 'proses', 'selesai', 'diambil'])->default('antrean');
            $table->enum('status_pembayaran', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }
};