<?php

namespace Tests\Unit;

use App\Models\Transaksi;
use Carbon\Carbon;
use Tests\TestCase;

class TransaksiJamAmbilTest extends TestCase
{
    public function test_menghitung_jam_ambil_paket_super_kilat(): void
    {
        $transaksi = new Transaksi();
        $transaksi->paket = 'super kilat';
        $transaksi->created_at = Carbon::parse('2026-08-01 10:00:00');

        // +6 Jam -> 2026-08-01 16:00
        $this->assertSame('01/08/2026 16:00', $transaksi->jam_ambil);
    }

    public function test_menghitung_jam_ambil_paket_kilat(): void
    {
        $transaksi = new Transaksi();
        $transaksi->paket = 'kilat';
        $transaksi->created_at = Carbon::parse('2026-08-01 10:00:00');

        // +24 Jam -> 2026-08-02 10:00
        $this->assertSame('02/08/2026 10:00', $transaksi->jam_ambil);
    }

    public function test_menghitung_jam_ambil_paket_reguler(): void
    {
        $transaksi = new Transaksi();
        $transaksi->paket = 'reguler';
        $transaksi->created_at = Carbon::parse('2026-08-01 10:00:00');

        // +48 Jam -> 2026-08-03 10:00
        $this->assertSame('03/08/2026 10:00', $transaksi->jam_ambil);
    }
}