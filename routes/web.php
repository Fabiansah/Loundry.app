<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PegawaiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\BukuKas;
use App\Models\Transaksi;
use App\Models\Pengeluaran;

/*
|--------------------------------------------------------------------------
| Web Routes - Laundry UMKM
|--------------------------------------------------------------------------
*/

// 1. RUTE PUBLIK & REDIRECT AWAL
Route::get('/', function () {
    return redirect()->route('login');
});

// Jalur Aktivasi Akun Kasir via Link WhatsApp (Bebas Akses / Tanpa Login)
Route::get('/aktivasi/{token}', [PegawaiController::class, 'showAktivasiForm'])->name('pegawai.aktivasi.form');
Route::post('/aktivasi/{token}', [PegawaiController::class, 'prosesAktivasi'])->name('pegawai.aktivasi.process');


// 2. GRUP RUTE INTERNAL (Wajib Login)
Route::middleware(['auth', 'verified'])->group(function () {

    // ---------------------------------------------------------------------
    // A. MANAJEMEN SHIFT & CLOSING KASIR (Bebas Middleware check.modal)
    // ---------------------------------------------------------------------
    
    // Form & Simpan Modal Awal
    Route::get('/modal-awal', function() {
        $kasAktif = BukuKas::where('user_id', auth()->id())
            ->where('tanggal', date('Y-m-d'))
            ->where('status', 'buka')
            ->exists();

        if ($kasAktif) {
            return redirect()->route('transaksi');
        }

        return view('transaksi.modal_awal');
    })->name('modal.create');

    Route::post('/modal-awal', function(Request $request) {
        $request->validate(['modal_awal' => 'required|numeric|min:0']);

        BukuKas::create([
            'user_id'    => auth()->id(),
            'modal_awal' => $request->modal_awal,
            'tanggal'    => date('Y-m-d'),
            'status'     => 'buka'
        ]);

        return redirect()->route('transaksi')->with('sukses', 'Modal awal berhasil disimpan. Selamat bekerja!');
    })->name('modal.store');

    // Logout Setelah Cetak Struk Closing
    Route::get('/logout-selesai', function(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('kasir.logoutAfterPrint');

    // Form & Eksekusi Tutup Kasir (Closing Shift)
    Route::get('/tutup-kasir', function() {
        $kasAktif = BukuKas::where('user_id', auth()->id())
            ->where('status', 'buka')
            ->latest()
            ->firstOrFail();

        $riwayatTransaksi     = Transaksi::where('created_at', '>=', $kasAktif->created_at)->latest()->get();
        $totalPendapatanShift = $riwayatTransaksi->where('status_pembayaran', 'lunas')->sum('total_harga');
        $totalPengeluaranKas  = Pengeluaran::where('user_id', auth()->id())->where('created_at', '>=', $kasAktif->created_at)->sum('jumlah');
        $estimasiUangLaci     = ($kasAktif->modal_awal + $totalPendapatanShift) - $totalPengeluaranKas;

        return view('transaksi.tutup_kasir', compact('kasAktif', 'riwayatTransaksi', 'totalPendapatanShift', 'totalPengeluaranKas', 'estimasiUangLaci'));
    })->name('kasir.tutupForm');

    Route::post('/tutup-kasir', function(Request $request) {
        $request->validate(['uang_fisik_laci' => 'required|numeric|min:0']);

        $kasAktif = BukuKas::where('user_id', auth()->id())
            ->where('status', 'buka')
            ->latest()
            ->firstOrFail();

        $totalPendapatanFinal = Transaksi::where('status_pembayaran', 'lunas')
            ->where('created_at', '>=', $kasAktif->created_at)
            ->sum('total_harga');

        $totalPengeluaranFinal = Pengeluaran::where('user_id', auth()->id())
            ->where('created_at', '>=', $kasAktif->created_at)
            ->sum('jumlah');

        $ekspektasiSistem = ($kasAktif->modal_awal + $totalPendapatanFinal) - $totalPengeluaranFinal;

        $kasAktif->update([
            'omzet_kotor'     => $totalPendapatanFinal,
            'laba_bersih'     => $totalPendapatanFinal * 0.70,
            'uang_fisik_laci' => $request->uang_fisik_laci,
            'selisih'         => $request->uang_fisik_laci - $ekspektasiSistem,
            'waktu_tutup'     => now(),
            'status'          => 'tutup'
        ]);

        return redirect()->route('kasir.printClosing', $kasAktif->id);
    })->name('kasir.tutupProcess');

    // Cetak Struk Closing
    Route::get('/tutup-kasir/print/{id}', function($id) {
        $bukuKas = BukuKas::findOrFail($id);

        $riwayatTransaksi = Transaksi::where('created_at', '>=', $bukuKas->created_at)
            ->where('created_at', '<=', $bukuKas->waktu_tutup ?? now())
            ->get();

        $pengeluarans = Pengeluaran::where('user_id', $bukuKas->user_id)
            ->where('created_at', '>=', $bukuKas->created_at)
            ->where('created_at', '<=', $bukuKas->waktu_tutup ?? now())
            ->get();

        return view('transaksi.print_closing', compact('bukuKas', 'riwayatTransaksi', 'pengeluarans'));
    })->name('kasir.printClosing');


    // ---------------------------------------------------------------------
    // B. RUTE OPERASIONAL (Dilindungi check.modal)
    // ---------------------------------------------------------------------
    Route::middleware(['check.modal'])->group(function () {

        // 1. Dashboard Utama (Menggunakan DashboardController Baru)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // 2. Transaksi Kasir
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::post('/transaksi/{id}/status/{status}', [TransaksiController::class, 'updateStatus'])->name('transaksi.updateStatus');
        Route::post('/transaksi/{id}/bayar', [TransaksiController::class, 'updatePembayaran'])->name('transaksi.updatePembayaran');
        Route::get('/transaksi/{id}/print', [TransaksiController::class, 'printNota'])->name('transaksi.print');
        Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');

        // 3. Kelola Pegawai (Menggunakan PegawaiController)
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

// 4. Pengeluaran Kas Operasional (Bisa Dipantau Admin & Kasir)
        Route::get('/pengeluaran', function() {
            // Ambil kasir/shift yang sedang AKTIF (status 'buka')
            $kasAktif = BukuKas::where('status', 'buka')
                ->when(auth()->user()->role === 'kasir', function ($query) {
                    return $query->where('user_id', auth()->id());
                })
                ->latest()
                ->first();

            if (!$kasAktif) {
                return view('pengeluaran.index', [
                    'modalKasir'         => 0,
                    'pengeluaranHariIni' => 0,
                    'pengeluarans'       => collect()
                ]);
            }

            // 1. Modal Awal Shift Aktif
            $modalKasir = $kasAktif->modal_awal;

            // 2. Riwayat Pengeluaran yang Diinput Sejak Shift Tersebut Dibuka
            $pengeluarans = Pengeluaran::where('created_at', '>=', $kasAktif->created_at)
                ->when(auth()->user()->role === 'kasir', function ($query) {
                    return $query->where('user_id', auth()->id());
                })
                ->latest()
                ->get();

            $pengeluaranHariIni = $pengeluarans->sum('jumlah');

            return view('pengeluaran.index', compact('modalKasir', 'pengeluaranHariIni', 'pengeluarans'));
        })->name('pengeluaran.index');

        Route::post('/pengeluaran', function(Request $request) {
            $request->validate([
                'keterangan' => 'required|string|max:255',
                'jumlah'     => 'required|numeric|min:1000'
            ]);

            Pengeluaran::create([
                'user_id'    => auth()->id(),
                'keterangan' => $request->keterangan,
                'jumlah'     => $request->jumlah,
                'tanggal'    => date('Y-m-d')
            ]);

            return redirect()->back()->with('sukses', 'Pengeluaran kas berhasil dicatat!');
        })->name('pengeluaran.store');

    });
});

require __DIR__.'/auth.php';