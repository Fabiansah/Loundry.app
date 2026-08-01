<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buku_kas', function (Blueprint $table) {
            // Menghapus baris jika sebelumnya sudah ada, lalu mendefinisikan struktur baru yang rapi
            $table->integer('omzet_kotor')->default(0)->after('modal_awal');
            $table->integer('laba_bersih')->default(0)->after('omzet_kotor');
        });
    }

    public function down(): void
    {
        Schema::table('buku_kas', function (Blueprint $table) {
            $table->dropColumn(['omzet_kotor', 'laba_bersih']);
        });
    }
};