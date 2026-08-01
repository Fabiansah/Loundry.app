<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->integer('total_pendapatan')->default(0)->after('modal_awal');
            $table->integer('uang_fisik_laci')->nullable()->after('total_pendapatan');
            $table->integer('selisih')->default(0)->after('uang_fisik_laci');
            $table->timestamp('waktu_tutup')->nullable()->after('tanggal');
            $table->enum('status', ['buka', 'tutup'])->default('buka')->after('waktu_tutup');
        });
    }

    public function down(): void
    {
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->dropColumn(['total_pendapatan', 'uang_fisik_laci', 'selisih', 'waktu_tutup', 'status']);
        });
    }
};