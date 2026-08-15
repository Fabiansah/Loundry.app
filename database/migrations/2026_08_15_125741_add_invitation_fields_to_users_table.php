<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    { 
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_hp')->nullable()->after('email');
            $table->string('invitation_token')->nullable()->unique()->after('password');
            $table->timestamp('token_expires_at')->nullable()->after('invitation_token');
            $table->enum('status_akun', ['pending', 'aktif'])->default('pending')->after('token_expires_at');
            // Ubah password agar nullable jika user belum membuat password sendiri
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'invitation_token', 'token_expires_at', 'status_akun']);
        });
    }
};