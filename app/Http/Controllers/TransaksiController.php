<?php

namespace App\Http\Controllers;

use App\Models\BukuKas;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    // 1. Menampilkan Halaman & Daftar Cucian Shift Aktif
    public function index()
    {
        // Ambil kasir/shift yang sedang AKTIF (status 'buka')
        $kasAktif = BukuKas::where('status', 'buka')
            ->when(auth()->user()->role === 'kasir', function ($query) {
                return $query->where('user_id', auth()->id());
            })
            ->latest()
            ->first();

        // Filter Daftar Cucian: Hanya yang masuk sejak shift kasir dibuka
        if ($kasAktif) {
            $transaksis = Transaksi::where('created_at', '>=', $kasAktif->created_at)
                ->latest()
                ->get();
        } else {
            // Jika belum buka modal atau sudah closing, daftar cucian kembali ke 0 (kosong)
            $transaksis = collect();
        }

        // Variabel penanda shift aktif untuk template blade
        $shiftAktif = $kasAktif;

        return view('transaksi.index', compact('transaksis', 'shiftAktif'));
    }

    // 2. Menyimpan Data Transaksi Baru
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|min:3|max:100',
            'nomor_hp'       => 'nullable|string|max:20',
            'berat_kg'       => 'required|numeric|min:0.1',
            'paket'          => 'required|string',
            'catatan'        => 'nullable|string|max:255',
        ]);

        // Penentuan harga paket per Kg
        $hargaPerKg = match ($request->paket) {
            'Kilat'       => 10000,
            'Super Kilat' => 15000,
            default       => 6000,
        };

        $totalHarga = (float) $request->berat_kg * $hargaPerKg;

        $transaksi = Transaksi::create([
            'user_id'                 => auth()->id(),
            'nama_pelanggan'          => trim($request->nama_pelanggan),
            'nomor_hp'                => $request->nomor_hp,
            'paket'                   => $request->paket,
            'berat_kg'                => $request->berat_kg,
            'harga_per_kg'            => $hargaPerKg,
            'total_harga'             => $totalHarga,
            'catatan'                 => $request->catatan,
            'status_laundry'          => 'antrean',
            'status_laundry_otomatis' => 'antrean',
            'status_pembayaran'       => 'belum_bayar',
        ]);

        return redirect()->route('transaksi')->with('sukses', 'Transaksi baru berhasil ditambahkan!');
    }

    // 3. Memperbarui Tahapan Status Laundry (Antrean -> Proses -> Selesai -> Diambil)
    public function updateStatus($id, $status)
    {
        $validStatus = ['antrean', 'proses', 'selesai', 'diambil'];
        
        if (!in_array($status, $validStatus)) {
            return redirect()->back()->with('error', 'Status tahapan cucian tidak valid!');
        }

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'status_laundry' => $status,
        ]);

        return redirect()->back()->with('sukses', 'Status cucian berhasil diperbarui!');
    }

    // 4. Memperbarui Status Pembayaran Menjadi Lunas
    public function updatePembayaran($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'status_pembayaran' => 'lunas',
        ]);

        return redirect()->back()->with('sukses', 'Status pembayaran berhasil dilunasi!');
    }

    // 5. Cetak Struk / Nota Thermal Transaksi
    public function printNota(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $autoPrint = $request->query('auto') === 'true';

        return view('transaksi.print', compact('transaksi', 'autoPrint'));
    }

    // 6. Menghapus Data Transaksi (Khusus Admin)
    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak: Anda tidak memiliki wewenang untuk menghapus data!');
        }

        $transaksi = Transaksi::findOrFail($id);
        $transaksi->delete();

        return redirect()->back()->with('sukses', 'Data transaksi berhasil dihapus dari sistem!');
    }
}