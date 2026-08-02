<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Tutup Kasir #{{ str_pad($bukuKas->id, 5, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Menggunakan font Courier / Monospace khas printer thermal */
        body {
            font-family: 'Courier New', Courier, monospace;
        }

        /* Styles khusus cetak printer thermal 80mm / 58mm */
        @media print {
            body { 
                background: white !important; 
                padding: 0 !important; 
                color: black !important;
            }
            .no-print { 
                display: none !important; 
            }
            .receipt-container { 
                box-shadow: none !important; 
                border: none !important; 
                width: 100% !important;
                max-width: 80mm !important; 
                margin: 0 auto !important; 
                padding: 0 !important; 
                color: black !important;
            }
            @page { 
                margin: 0; 
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-mono antialiased min-h-screen flex flex-col items-center justify-center p-4 text-black">

<!-- TOMBOL NAVIGASI (TIDAK IKUT TERCETAK) -->
<div class="no-print mb-6 flex items-center gap-2.5 bg-white/80 backdrop-blur-md p-1.5 rounded-2xl border border-slate-200/80 shadow-sm transition-all duration-200 hover:shadow-md">
    <a href="{{ route('transaksi') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 text-xs font-semibold py-2 px-3.5 rounded-xl transition-all duration-200 flex items-center gap-1.5 font-sans active:scale-95">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali
    </a>
    <button onclick="window.print()" class="bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2 px-4 rounded-xl shadow-sm hover:shadow transition-all duration-200 flex items-center gap-1.5 font-sans active:scale-95">
        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
        </svg>
        Cetak Nota
    </button>
</div>

    <!-- CONTAINER STRUK STRUKTUR PERSIS FOTO -->
    <div class="receipt-container bg-white w-full max-w-[340px] p-6 rounded-2xl shadow-xl border border-black text-black text-xs leading-relaxed">
        
        <!-- HEADER KOTA -->
        <div class="mb-2 flex items-center justify-center gap-2 text-center">
            <div class="w-7 h-7 bg-black rounded-md flex items-center justify-center text-white shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <rect x="4" y="2" width="16" height="20" rx="3" ry="3"></rect>
                    <line x1="8" y1="6" x2="10" y2="6" stroke-linecap="round"></line>
                    <circle cx="16" cy="6" r="1" fill="currentColor"></circle>
                    <circle cx="12" cy="14" r="5"></circle>
                    <path d="M10 13c1-1 3-1 4 0s3 1 4 0" stroke-linecap="round"></path>
                </svg>
            </div>
            <div>
                <div class="text-[10px] font-black uppercase tracking-wide">LAUNDRY UMKM</div>
                <div class="text-[8px] uppercase tracking-wider">Madiun, Jawa Timur</div>
            </div>
        </div>

        <div class="text-center my-1 tracking-widest font-bold">
            ================================
        </div>

        <!-- JUDUL LAPORAN -->
        <div class="text-center py-1">
            <h1 class="text-xs font-black uppercase tracking-wider text-black">LAPORAN TUTUP KASIR</h1>
            <h2 class="text-[11px] font-bold uppercase tracking-wider text-black">TRANSAKSI PENJUALAN</h2>
        </div>

        <!-- INFO SHIFT KASIR -->
        <div class="py-2 space-y-0.5 text-xs">
            <div class="flex justify-between">
                <span>Kasir</span>
                <span>: {{ $bukuKas->user->name ?? auth()->user()->name }}</span>
            </div>
            <div class="flex justify-between">
                <span>Waktu Buka</span>
                <span>: {{ \Carbon\Carbon::parse($bukuKas->created_at)->format('d M Y, H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Waktu Tutup</span>
                <span>: {{ \Carbon\Carbon::parse($bukuKas->waktu_tutup ?? now())->format('d M Y, H:i') }}</span>
            </div>
        </div>

        <!-- PERHITUNGAN MODAL & PENERIMAAN -->
        <div class="py-2 border-t border-black space-y-1">
            <div class="flex justify-between">
                <span>Modal Awal</span>
                <span class="font-bold">{{ number_format($bukuKas->modal_awal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tunai</span>
                <span class="font-bold">{{ number_format($bukuKas->omzet_kotor, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Transfer</span>
                <span class="font-bold">0</span>
            </div>
            <div class="flex justify-between">
                <span>- QRIS</span>
                <span class="font-bold">0</span>
            </div>
            <div class="flex justify-between font-bold border-t border-dashed border-black pt-1">
                <span class="w-2/3 leading-tight">Total Penerimaan Kasir</span>
                <span>{{ number_format($bukuKas->omzet_kotor, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="text-center my-1 tracking-widest font-bold">
            --------------------------------
        </div>

        <!-- TOTAL PENERIMAAN & KAS KELUAR -->
        <div class="py-1 space-y-1">
            <div class="flex justify-between font-bold">
                <span>Total Penerimaan</span>
                <span>{{ number_format($bukuKas->omzet_kotor, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="text-center my-1 tracking-widest font-bold">
            --------------------------------
        </div>

        <div class="py-1 space-y-1">
            <div class="flex justify-between">
                <span>Kas Keluar</span>
                <span class="font-bold">{{ number_format($pengeluarans->sum('jumlah'), 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="text-center my-1 tracking-widest font-bold">
            --------------------------------
        </div>

        <!-- SALDO AKHIR -->
        <div class="py-1">
            <div class="flex justify-between font-black text-sm">
                <span>Saldo Akhir</span>
                <span>{{ number_format(($bukuKas->modal_awal + $bukuKas->omzet_kotor) - $pengeluarans->sum('jumlah'), 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- REKAP NOTA & STATUS -->
        <div class="py-2 border-t border-black space-y-1">
            <div class="flex justify-between">
                <span>Transaksi Selesai</span>
                <span class="font-bold">{{ $riwayatTransaksi->where('status_pembayaran', 'lunas')->count() }}</span>
            </div>
            <div class="flex justify-between">
                <span>Transaksi Belum Terbayar</span>
                <span class="font-bold">{{ $riwayatTransaksi->where('status_pembayaran', 'belum_bayar')->count() }}</span>
            </div>
        </div>

        <div class="text-center my-1 tracking-widest font-bold">
            --------------------------------
        </div>

        <!-- COMPARISON TUNAI SISTEM VS AKTUAL & SELISIH -->
        @php
            $totalTunaiSistem = ($bukuKas->modal_awal + $bukuKas->omzet_kotor) - $pengeluarans->sum('jumlah');
        @endphp
        <div class="py-1 space-y-1">
            <div class="flex justify-between">
                <span>Total Tunai Sistem</span>
                <span class="font-bold">{{ number_format($totalTunaiSistem, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Total Tunai Aktual</span>
                <span class="font-bold">{{ number_format($bukuKas->uang_fisik_laci, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between font-black">
                <span>Selisih</span>
                <span>{{ number_format($bukuKas->selisih, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="text-center my-1 tracking-widest font-bold">
            ================================
        </div>

        <!-- RIWAYAT TRANSAKSI PENJUALAN (BAGIAN BAWAH) -->
        <div class="py-2">
            <span class="font-black uppercase block mb-1">RINCIAN TRANSAKSI ({{ $riwayatTransaksi->count() }})</span>
            <div class="space-y-1">
                @forelse($riwayatTransaksi as $tx)
                    <div class="flex justify-between text-[11px]">
                        <span>#{{ str_pad($tx->id, 4, '0', STR_PAD_LEFT) }} {{ \Illuminate\Support\Str::limit($tx->nama_pelanggan, 12) }}</span>
                        <span class="font-bold">
                            {{ $tx->status_pembayaran == 'lunas' ? number_format($tx->total_harga, 0, ',', '.') : 'Belum' }}
                        </span>
                    </div>
                @empty
                    <p class="text-[10px] italic">Tidak ada transaksi pada shift ini.</p>
                @endforelse
            </div>
        </div>

        <div class="text-center my-1 tracking-widest font-bold">
            --------------------------------
        </div>

        <!-- RIWAYAT PENGELUARAN KAS (BAGIAN PALING BAWAH) -->
        <div class="py-2">
            <span class="font-black uppercase block mb-1">RINCIAN KAS KELUAR ({{ $pengeluarans->count() }})</span>
            <div class="space-y-1">
                @forelse($pengeluarans as $out)
                    <div class="flex justify-between text-[11px]">
                        <span>• {{ \Illuminate\Support\Str::limit($out->keterangan, 16) }}</span>
                        <span class="font-bold">-{{ number_format($out->jumlah, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <p class="text-[10px] italic">Tidak ada pengeluaran kas.</p>
                @endforelse
            </div>
        </div>

        <div class="text-center my-1 tracking-widest font-bold">
            ================================
        </div>

        <!-- FOOTER DOKUMEN -->
        <div class="pt-2 text-center text-[10px] space-y-0.5">
            <p class="text-[9px]">Dicetak: {{ date('d/m/Y H:i:s') }}</p>
        </div>

    </div>

    <!-- JAVASCRIPT OTOMATIS POP-UP DIALOG PRINT -->
    <script>
        window.onload = function() {
            window.print();
        }
    </script>

<script>
    setTimeout(function () {
        window.location.href = "{{ route('kasir.logoutAfterPrint') }}";
    }, 4000);
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400&display=swap');
    
    body {
        font-family: 'Courier Prime', 'Courier New', Courier, monospace;
        background-color: #f1f5f9;
    }

    /* Pengaturan CSS Khusus Mode Cetak / Print */
    @media print {
        /* Memaksa browser mencetak seluruh gambar, logo, dan background warna */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }

        /* Sembunyikan tombol navigasi saat diprint */
        .no-print {
            display: none !important;
        }

        body {
            background-color: #ffffff !important;
            padding: 0 !important;
        }

        .receipt-container {
            box-shadow: none !important;
            border: none !important;
            width: 100% !important;
            max-width: 80mm !important;
            margin: 0 auto !important;
            padding: 0 !important;
        }
    }
</style>
</body>
</html>