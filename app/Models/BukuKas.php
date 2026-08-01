<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukuKas extends Model
{
    protected $fillable = ['user_id', 'modal_awal', 'omzet_kotor', 'laba_bersih', 'uang_fisik_laci', 'selisih', 'tanggal', 'waktu_tutup', 'status'];
}
