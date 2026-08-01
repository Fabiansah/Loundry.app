<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $fillable = ['user_id', 'keterangan', 'jumlah', 'tanggal'];
}