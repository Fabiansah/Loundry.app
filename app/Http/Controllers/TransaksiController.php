<?php

namespace App\Http\Controllers;

use App\Models\BukuKas;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    // 1. Menampilkan Halaman & Daftar Data Transaksi
    public function index()
    {
        // Pengondisian: Jika yang login adalah Admin, tampilkan semua transaksi terbaru
        if (auth()->user()->role === 'admin') {
            $transaksis = Transaksi::latest()->get();
            return view('transaksi.index', compact('transaksis'));
        }

        // Jika yang login adalah Kasir: Tampilkan riwayat khusus dari jam buka kasir aktif saja (Efek otomatis reset)
        $kasAktif = BukuKas::where('user_id', auth()->id())
            ->where('tanggal', date('Y-m-d'))
            ->where('status', 'buka')
            ->first();

        if ($kasAktif) {
            $transaksis = Transaksi::where('created_at', '>=', $kasAktif->created_at)
                ->latest()
                ->get();
        } else {
            $transaksis = collect(); // Kosong jika belum buka kasir
        }

        return view('transaksi.index', compact('transaksis'));
    }

    // 2. Menyimpan Data Transaksi Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|min:3',
            'berat_kg' => 'required|numeric|min:0.1',
            'paket' => 'required|string',
        ]);

        $hargaPerKg = 6000;

        if ($request->paket == 'Kilat') {
            $hargaPerKg = 10000;
        } elseif ($request->paket == 'Super Kilat') {
            $hargaPerKg = 15000;
        }

        $totalHarga = $request->berat_kg * $hargaPerKg;

        $transaksi = Transaksi::create([
            'user_id' => auth()->id(),
            'nama_pelanggan' => $request->nama_pelanggan,
            'nomor_hp' => $request->nomor_hp,
            'paket' => $request->paket,
            'berat_kg' => $request->berat_kg,
            'harga_per_kg' => $hargaPerKg,
            'total_harga' => $totalHarga,
            'catatan' => $request->catatan,
            'status_laundry' => 'antrean',
            'status_laundry_otomatis' => 'antrean',
            'status_pembayaran' => 'belum_bayar',
        ]);

        return redirect()->route('transaksi')->with('sukses', 'Transaksi berhasil disimpan!');
    }

    // 3. Memperbarui Tahapan Status Laundry (Antrean -> Proses -> Selesai -> Diambil)
    public function updateStatus($id, $status)
    {
        $transaksi = Transaksi::findOrFail($id);

        $transaksi->update([
            'status_laundry' => $status
        ]);

        return redirect()->back()->with('sukses', 'Status laundry berhasil diperbarui!');
    }

    // 4. Memperbarui Status Pembayaran Menjadi Lunas
    public function updatePembayaran($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $transaksi->update([
            'status_pembayaran' => 'lunas'
        ]);

        return redirect()->back()->with('sukses', 'Status pembayaran berhasil dilunasi!');
    }

// 5. Cetak Struk / Nota Thermal Transaksi
    public function printNota(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        
        // Cek apakah ada parameter ?auto=true di URL
        $autoPrint = $request->query('auto') == 'true';

        // Kirimkan variabel $transaksi dan $autoPrint ke view
        return view('transaksi.print', compact('transaksi', 'autoPrint'));
    }

    // 6. Menghapus Data Transaksi (Khusus Admin)
    public function destroy($id)
    {
        // Pengaman tingkat sistem: pastikan yang menghapus benar-benar admin
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus data!');
        }

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->back()->with('sukses', 'Data transaksi berhasil dihapus dari sistem!');
    }
}