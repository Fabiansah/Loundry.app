<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @property string $paket
 * @property string|Carbon $created_at
 * @property-read string $jam_ambil
 */
class Transaksi extends Model
{
    protected $guarded = ['id'];

    /**
     * Accessor untuk Menghitung Estimasi Jadwal Jam Ambil Pelanggan
     */
    public function getJamAmbilAttribute(): string
    {
        $waktuDibuat = $this->created_at ? Carbon::parse($this->created_at) : now();
        $paket = strtolower($this->paket ?? 'reguler');

        // 1. Super Kilat -> +6 Jam
        if (str_contains($paket, 'super kilat')) {
            return $waktuDibuat->addHours(6)->format('d/m/Y H:i');
        }

        // 2. Kilat -> +24 Jam (1 Hari)
        if (str_contains($paket, 'kilat')) {
            return $waktuDibuat->addHours(24)->format('d/m/Y H:i');
        }

        // 3. Reguler -> +48 Jam (2 Hari)
        return $waktuDibuat->addHours(48)->format('d/m/Y H:i');
    }
}