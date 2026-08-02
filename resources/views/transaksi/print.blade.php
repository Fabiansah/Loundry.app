<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Struk #{{ $transaksi->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Pengaturan Khusus untuk Hasil Cetakan Thermal & Kertas */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 80mm !important; /* Standar Lebar Thermal Struk */
                margin: 0 auto !important;
                padding: 0 !important;
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

    <!-- STRUK NOTA -->
    <div class="receipt-container bg-white w-full max-w-[340px] p-6 rounded-2xl shadow-xl border border-black text-black text-xs leading-relaxed">

        <!-- HEADER TOKO -->
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

        <div class="text-center py-1">
            <h1 class="text-xs font-black uppercase tracking-wider text-black">NOTA TRANSAKSI</h1>
            <h2 class="text-[11px] font-bold uppercase tracking-wider text-black">BUKTI PENERIMAAN</h2>
        </div>

        <div class="py-2 space-y-0.5 text-xs">
            <div class="flex justify-between">
                <span>No. Nota</span>
                <span>: #{{ str_pad($transaksi->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tanggal</span>
                <span>: {{ $transaksi->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Jam Ambil</span>
                <span>: {{ $transaksi->jam_ambil }} WIB</span>
            </div>
            <div class="flex justify-between">
                <span>Pelanggan</span>
                <span>: {{ $transaksi->nama_pelanggan }}</span>
            </div>
            @if($transaksi->nomor_hp)
            <div class="flex justify-between">
                <span>No. HP</span>
                <span>: {{ $transaksi->nomor_hp }}</span>
            </div>
            @endif
        </div>

        <div class="py-2 border-t border-black space-y-1">
            <div class="flex justify-between items-start">
                <div>
                    <span class="font-bold block">Layanan {{ $transaksi->paket ?? 'Reguler' }}</span>
                    <span class="text-[10px]">{{ $transaksi->berat_kg }} Kg &times; Rp {{ number_format($transaksi->harga_per_kg ?? 6000, 0, ',', '.') }}</span>
                </div>
                <span class="font-bold">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>

            @if($transaksi->catatan)
            <div class="border border-black rounded p-2 text-[10px] italic">
                Catatan: {{ $transaksi->catatan }}
            </div>
            @endif
        </div>

        <div class="text-center my-1 tracking-widest font-bold">
            --------------------------------
        </div>

        <div class="py-1 space-y-1">
            <div class="flex justify-between font-black text-sm">
                <span>Total</span>
                <span>Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Status Bayar</span>
                <span class="font-bold">{{ $transaksi->status_pembayaran == 'lunas' ? 'LUNAS' : 'BELUM BAYAR' }}</span>
            </div>
        </div>

        <div class="text-center my-1 tracking-widest font-bold">
            ================================
        </div>

        <div class="pt-2 text-center text-[10px] space-y-0.5">
            <p class="font-bold">Terima kasih atas kepercayaan Anda!</p>
            <p>Simpan nota ini sebagai bukti pengambilan</p>
        </div>

    </div>

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