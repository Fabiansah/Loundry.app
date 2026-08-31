<?php

namespace App\Http\Controllers;

use App\Models\BukuKas;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ==========================================
        // 1. LOGIKA SHIFT KASIR AKTIF (MURNI STATUS 'BUKA')
        // ==========================================
        $shiftAktif = BukuKas::where('status', 'buka')
            ->when(auth()->user()->role === 'kasir', function ($query) {
                return $query->where('user_id', auth()->id());
            })
            ->latest()
            ->first();

        $waktuMulaiShift = $shiftAktif ? $shiftAktif->created_at : null;

        if ($shiftAktif && $waktuMulaiShift) {
            // Hitung transaksi yang terjadi sejak shift dibuka
            $transaksiShift = Transaksi::where('created_at', '>=', $waktuMulaiShift);

            $pendapatanLunas = (clone $transaksiShift)
                ->where('status_pembayaran', 'lunas')
                ->sum('total_harga');

            $belumTerbayar = (clone $transaksiShift)
                ->where('status_pembayaran', 'belum_bayar')
                ->sum('total_harga');

            $dalamProsesCuci = (clone $transaksiShift)
                ->whereIn('status_laundry', ['proses', 'cuci', 'antrean', 'antre'])
                ->count();

            $bebanMesinAktif = (clone $transaksiShift)
                ->whereIn('status_laundry', ['proses', 'cuci'])
                ->sum('berat_kg');

            $siapDiambil = (clone $transaksiShift)
                ->whereIn('status_laundry', ['selesai', 'siap_ambil'])
                ->count();

            // Total pengeluaran kasir pada shift aktif
            $pengeluaranShift = Pengeluaran::where('created_at', '>=', $waktuMulaiShift)
                ->when(auth()->user()->role === 'kasir', function ($query) {
                    return $query->where('user_id', auth()->id());
                })
                ->sum('jumlah');

            $modalAwalKasir = $shiftAktif->modal_awal ?? 0;
            $estimasiUangKas = ($modalAwalKasir + $pendapatanLunas) - $pengeluaranShift;
        } else {
            // JIKA KASIR SUDAH CLOSING / SHIFT TUTUP -> SEMUA KARTU RESET KE 0
            $pendapatanLunas  = 0;
            $belumTerbayar    = 0;
            $dalamProsesCuci  = 0;
            $bebanMesinAktif  = 0;
            $siapDiambil      = 0;
            $pengeluaranShift = 0;
            $modalAwalKasir   = 0;
            $estimasiUangKas  = 0;
        }

        // ==========================================
        // 2. LOGIKA CHART FLUKTUASI PENDAPATAN BULANAN (OPTIMAL)
        // ==========================================
        $selectedMonth = (int) $request->get('bulan', Carbon::now()->month);
        $selectedYear  = (int) $request->get('tahun', Carbon::now()->year);

        $daysInMonth = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->daysInMonth;
        $chartLabels = [];
        $chartData   = [];

        // Agregasi SQL langsung untuk performa cepat
        $omzetPerHari = Transaksi::select(
                DB::raw('DAY(created_at) as hari'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('status_pembayaran', 'lunas')
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->groupBy(DB::raw('DAY(created_at)'))
            ->pluck('total', 'hari')
            ->toArray();

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $chartLabels[] = (string) $day;
            $chartData[]   = (int) ($omzetPerHari[$day] ?? 0);
        }

        $totalOmzetBulanIni = array_sum($chartData);

        // ==========================================
        // 3. RIWAYAT TRANSAKSI TERKINI (GLOBAL)
        // ==========================================
        $transaksiTerkini = Transaksi::latest()->take(7)->get();

        return view('dashboard', compact(
            'shiftAktif',
            'pendapatanLunas',
            'belumTerbayar',
            'dalamProsesCuci',
            'bebanMesinAktif',
            'siapDiambil',
            'pengeluaranShift',
            'modalAwalKasir',
            'estimasiUangKas',
            'chartLabels',
            'chartData',
            'selectedMonth',
            'selectedYear',
            'totalOmzetBulanIni',
            'transaksiTerkini'
        ));
    }
}