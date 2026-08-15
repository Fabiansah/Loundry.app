<?php

use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\PegawaiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Models\BukuKas;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Keamanan Terpisah (Laundry UMKM)
|--------------------------------------------------------------------------
*/

// 1. KUNCI HALAMAN UTAMA: Jika ada yang akses http://127.0.0.1:8000/ langsung lempar ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// 2. GRUP RUTE INTERNAL (TERPISAH & TERPROTEKSI PENUH)
Route::middleware(['auth', 'verified'])->group(function () {

    // ---------------------------------------------------------------------
    // A. RUTE BEBAS CEK MODAL (Mencegah Loop Redirect saat Buka/Tutup Kasir)
    // ---------------------------------------------------------------------
    
    // 1. Jalur Pengisian Modal Awal Kasir
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
            'user_id' => auth()->id(),
            'modal_awal' => $request->modal_awal,
            'tanggal' => date('Y-m-d'),
            'status' => 'buka'
        ]);

        return redirect()->route('transaksi')->with('sukses', 'Modal awal berhasil disimpan. Selamat bekerja!');
    })->name('modal.store');

    Route::get('/logout-selesai', function(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('kasir.logoutAfterPrint');

    // 2. Form Penutupan Kasir (Closing Shift)
    Route::get('/tutup-kasir', function() {
        $kasAktif = BukuKas::where('user_id', auth()->id())
            ->where('tanggal', date('Y-m-d'))
            ->where('status', 'buka')
            ->firstOrFail();

        $riwayatTransaksi = Transaksi::where('created_at', '>=', $kasAktif->created_at)->latest()->get();
        $totalPendapatanShift = $riwayatTransaksi->where('status_pembayaran', 'lunas')->sum('total_harga');
        $totalPengeluaranKas = Pengeluaran::where('user_id', auth()->id())->where('created_at', '>=', $kasAktif->created_at)->sum('jumlah');
        $estimasiUangLaci = ($kasAktif->modal_awal + $totalPendapatanShift) - $totalPengeluaranKas;

        return view('transaksi.tutup_kasir', compact('kasAktif', 'riwayatTransaksi', 'totalPendapatanShift', 'totalPengeluaranKas', 'estimasiUangLaci'));
    })->name('kasir.tutupForm');

    // 3. Proses Eksekusi Tutup Kasir
    Route::post('/tutup-kasir', function(Request $request) {
        $request->validate(['uang_fisik_laci' => 'required|numeric|min:0']);

        $kasAktif = BukuKas::where('user_id', auth()->id())
            ->where('tanggal', date('Y-m-d'))
            ->where('status', 'buka')
            ->firstOrFail();

        $totalPendapatanFinal = Transaksi::where('status_pembayaran', 'lunas')
            ->where('created_at', '>=', $kasAktif->created_at)
            ->sum('total_harga');

        $totalPengeluaranFinal = Pengeluaran::where('user_id', auth()->id())
            ->where('created_at', '>=', $kasAktif->created_at)
            ->sum('jumlah');

        $ekspektasiSistem = ($kasAktif->modal_awal + $totalPendapatanFinal) - $totalPengeluaranFinal;

        $kasAktif->update([
            'omzet_kotor' => $totalPendapatanFinal,
            'laba_bersih' => $totalPendapatanFinal * 0.70,
            'uang_fisik_laci' => $request->uang_fisik_laci,
            'selisih' => $request->uang_fisik_laci - $ekspektasiSistem,
            'waktu_tutup' => now(),
            'status' => 'tutup'
        ]);

        return redirect()->route('kasir.printClosing', $kasAktif->id);
    })->name('kasir.tutupProcess');

    // 4. Halaman Cetak Struk Bukti Closing Kasir
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
    // B. RUTE OPERASIONAL (Dilindungi check.modal untuk Kasir)
    // ---------------------------------------------------------------------
    Route::middleware(['check.modal'])->group(function () {

        // Dashboard Utama
        Route::get('/dashboard', function () {
            $totalOmzet = Transaksi::where('status_pembayaran', 'lunas')->sum('total_harga');
            $totalPiutang = Transaksi::where('status_pembayaran', 'belum_bayar')->sum('total_harga');
            $cucianAktif = Transaksi::whereIn('status_laundry', ['antrean', 'proses'])->count();
            $siapDiambil = Transaksi::where('status_laundry', 'selesai')->count();
            $totalBeratAktif = Transaksi::whereIn('status_laundry', ['antrean', 'proses'])->sum('berat_kg') ?? 0;
            
            $tujuhHariLalu = now()->subDays(7);
            $riwayatTerbaru = Transaksi::where('created_at', '>=', $tujuhHariLalu)->latest()->get();
            $jumlahTransaksi = $riwayatTerbaru->count();

            return view('dashboard', compact('totalOmzet', 'totalPiutang', 'cucianAktif', 'siapDiambil', 'totalBeratAktif', 'riwayatTerbaru', 'jumlahTransaksi'));
        })->name('dashboard');

        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('transaksi.store');
        Route::post('/transaksi/{id}/status/{status}', [TransaksiController::class, 'updateStatus'])->name('transaksi.updateStatus');
        Route::post('/transaksi/{id}/bayar', [TransaksiController::class, 'updatePembayaran'])->name('transaksi.updatePembayaran');
        Route::get('/transaksi/{id}/print', [TransaksiController::class, 'printNota'])->name('transaksi.print');
        Route::delete('/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');
        
        // Kelola Pegawai Kasir (Khusus Admin)
        Route::get('/pegawai', function() {
            if(auth()->user()->role !== 'admin') abort(403);
            $pegawais = User::where('role', 'kasir')->get();
            return view('pegawai.index', compact('pegawais'));
        })->name('pegawai.index');

        Route::post('/pegawai', function(Request $request) {
            if(auth()->user()->role !== 'admin') abort(403);
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'role' => 'kasir',
                'password' => Hash::make($request->password),
            ]);

            return redirect()->back()->with('sukses', 'Pegawai Kasir baru berhasil didaftarkan!');
        })->name('pegawai.store');   

        Route::delete('/pegawai/{id}', function($id) {
            if(auth()->user()->role !== 'admin') abort(403);
            
            if(auth()->id() == $id) {
                return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun Admin Anda sendiri!');
            }

            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->back()->with('sukses', 'Akun pegawai berhasil dihapus permanen dari sistem!');
        })->name('pegawai.destroy');

        // Pengeluaran Kasir
        Route::get('/pengeluaran', function() {
            $kasAktif = BukuKas::where('user_id', auth()->id())
                ->where('tanggal', date('Y-m-d'))
                ->where('status', 'buka')
                ->first();

            if (!$kasAktif) {
                return view('pengeluaran.index', [
                    'modalKasir' => 0,
                    'pengeluaranHariIni' => 0,
                    'pengeluarans' => collect()
                ]);
            }

            // 1. Murni Modal Awal Kasir saat Pembukaan
            $modalKasir = $kasAktif->modal_awal;

            // 2. Riwayat & Total Pengeluaran Kas Shift Aktif
            $pengeluarans = Pengeluaran::where('user_id', auth()->id())
                ->where('created_at', '>=', $kasAktif->created_at)
                ->latest()
                ->get();

            $pengeluaranHariIni = $pengeluarans->sum('jumlah');

            return view('pengeluaran.index', compact(
                'modalKasir',
                'pengeluaranHariIni',
                'pengeluarans'
            ));
        })->name('pengeluaran.index');

        Route::post('/pengeluaran', function(Request $request) {
            $request->validate([
                'keterangan' => 'required|string|max:255',
                'jumlah' => 'required|numeric|min:1000'
            ]);

            Pengeluaran::create([
                'user_id' => auth()->id(),
                'keterangan' => $request->keterangan,
                'jumlah' => $request->jumlah,
                'tanggal' => date('Y-m-d')
            ]);

            return redirect()->back()->with('sukses', 'Pengeluaran kas mendadak berhasil dicatat!');
        })->name('pengeluaran.store');

        // --- RUTE PUBLIK: AKTIVASI KASIR VIA LINK WHATSAPP ---
        Route::get('/aktivasi/{token}', [PegawaiController::class, 'showAktivasiForm'])->name('pegawai.aktivasi.form');
        Route::post('/aktivasi/{token}', [PegawaiController::class, 'prosesAktivasi'])->name('pegawai.aktivasi.process');

        // --- RUTE INTERNAL ADMIN (Di dalam middleware auth) ---
        Route::middleware(['auth', 'verified'])->group(function () {
            Route::middleware(['check.modal'])->group(function () {
                // Kelola Pegawai
                Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
                Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
                Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
            });
        });
    });
});

// Sistem Autentikasi bawaan Laravel
require __DIR__.'/auth.php';