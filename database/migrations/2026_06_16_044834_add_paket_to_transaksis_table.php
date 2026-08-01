<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            // Menambahkan kolom nama paket dan harga per kg setelah kolom nomor hp
            $table->string('paket')->default('Reguler')->after('nomor_hp');
            $table->integer('harga_per_kg')->default(6000)->change(); // Memastikan tipe data cocok
        });
    }

    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn('paket');
        });
    }
};