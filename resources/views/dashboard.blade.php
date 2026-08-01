<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Laundry UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Mengunci scrollbar vertikal agar navbar tidak bergeser antar halaman */
        html {
            overflow-y: scroll;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased">

<nav class="bg-white shadow border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center gap-2.5">
                    <div class="w-9 h-9 bg-indigo-50 border border-indigo-100 rounded-xl flex items-center justify-center text-indigo-600 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <!-- Frame Mesin Cuci -->
                            <rect x="4" y="2" width="16" height="20" rx="3" ry="3"></rect>
                            <!-- Tombol Kontrol -->
                            <line x1="8" y1="6" x2="10" y2="6" stroke-linecap="round"></line>
                            <circle cx="16" cy="6" r="1" fill="currentColor"></circle>
                            <!-- Pintu Tabung -->
                            <circle cx="12" cy="14" r="5"></circle>
                            <!-- Gelombang Air -->
                            <path d="M10 13c1-1 3-1 4 0s3 1 4 0" stroke-linecap="round"></path>
                        </svg>
                    </div>
                </div>
                <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'border-indigo-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm">
                        Dashboard
                    </a>
                    <a href="{{ route('transaksi') }}" class="{{ request()->routeIs('transaksi') ? 'border-indigo-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm">
                        Kelola Transaksi
                    </a>
                    <a href="{{ route('pengeluaran.index') }}" class="{{ request()->routeIs('pengeluaran.index') ? 'border-indigo-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm">
                        Pengeluaran Kas
                    </a>
                    @if(auth()->user()->role == 'admin')
                        <a href="{{ route('pegawai.index') }}" class="{{ request()->routeIs('pegawai.index') ? 'border-indigo-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700' }} inline-flex items-center px-1 pt-1 border-b-2 text-sm">
                            Kelola Pegawai
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <span class="text-sm font-semibold text-slate-900 block">Halo, {{ auth()->user()->name }}</span>
                    <span class="text-[10px] bg-slate-100 text-slate-600 font-bold px-2 py-0.5 rounded uppercase tracking-wider">{{ auth()->user()->role }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold py-2 px-4 rounded transition shadow-sm">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

    <!-- KONTEN UTAMA -->
    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Ringkasan Analitik</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Pantau performa keuangan dan antrean produksi operasional laundry secara real-time.</p>
                </div>

                @if(auth()->user()->role === 'kasir')
                    <a href="{{ route('kasir.tutupForm') }}" class="inline-flex items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-4 py-2.5 text-sm font-bold text-rose-600 shadow-sm transition-all hover:bg-rose-100">
                        Akhiri Kerja
                    </a>
                @endif
            </div>

            <!-- GRID KARTU METRIK UTAMA -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
                
                <!-- KARTU 1: TOTAL OMZET (KHUSUS ADMIN) -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pendapatan Lunas</span>
                        @if(auth()->user()->role == 'admin')
                            <h3 class="text-xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
                        @else
                            <h3 class="text-xs font-bold text-slate-600 mt-3 flex items-center gap-1">Khusus Admin</h3>
                        @endif
                    </div>
                </div>

                <!-- KARTU 2: TOTAL PIUTANG (KHUSUS ADMIN) -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Belum Terbayar</span>
                        @if(auth()->user()->role == 'admin')
                            <h3 class="text-xl font-extrabold text-rose-600 mt-1">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h3>
                        @else
                            <h3 class="text-xs font-bold text-slate-600 mt-3 flex items-center gap-1">Khusus Admin</h3>
                        @endif
                    </div>
                </div>

                <!-- KARTU 3: CUCIAN AKTIF -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dalam Proses Cuci</span>
                        <h3 class="text-xl font-extrabold text-amber-600 mt-1">{{ $cucianAktif }} Nota</h3>
                    </div>
                </div>

                <!-- KARTU 4: BEBAN BERAT CUCIAN -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Beban Mesin Aktif</span>
                        <h3 class="text-xl font-extrabold text-blue-600 mt-1">{{ $totalBeratAktif }} Kg</h3>
                    </div>
                </div>

                <!-- KARTU 5: SIAP DIAMBIL -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Siap Diambil</span>
                        <h3 class="text-xl font-extrabold text-indigo-600 mt-1">{{ $siapDiambil }} Nota</h3>
                    </div>
                </div>

            </div>

            <!-- SEKSI DAFTAR RIWAYAT TRANSAKSI -->
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Aktivitas Transaksi Terkini</h2>
                        @if($jumlahTransaksi == 0)
                            <p class="text-xs text-slate-400 mt-0.5">Tidak ada riwayat pemesanan laundry dalam 7 hari terakhir.</p>
                        @elseif($jumlahTransaksi == 1)
                            <p class="text-xs text-slate-400 mt-0.5">Menampilkan 1 transaksi dari kasir dalam 7 hari terakhir.</p>
                        @else
                            <p class="text-xs text-slate-400 mt-0.5">Menampilkan {{ $jumlahTransaksi }} transaksi dari kasir dalam 7 hari terakhir.</p>
                        @endif
                    </div>
                    <a href="{{ route('transaksi') }}" class="text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-lg transition-all flex items-center gap-1">
                        Buka Kasir &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Data Pelanggan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Berat Cucian</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Total Nominal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Progres Operasional</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status Invoice</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse ($riwayatTerbaru as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="block font-bold text-slate-900">{{ $item->nama_pelanggan }}</span>
                                        <span class="text-xs text-slate-400 block mt-0.5">{{ $item->nomor_hp ?? 'Tanpa No. HP' }}</span>
                                        <span class="mt-1 inline-block text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $item->paket ?? 'Reguler' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600 font-semibold">{{ $item->berat_kg }} Kg</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-900 font-extrabold">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full 
                                            {{ $item->status_laundry == 'antrean' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                            {{ $item->status_laundry == 'proses' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                            {{ $item->status_laundry == 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                            {{ $item->status_laundry == 'diambil' ? 'bg-slate-100 text-slate-600' : '' }}
                                        ">
                                            ● {{ ucfirst($item->status_laundry) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full 
                                            {{ $item->status_pembayaran == 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}
                                        ">
                                            {{ $item->status_pembayaran == 'lunas' ? '✓ Lunas' : '⚡ Belum Bayar' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-slate-400 italic">Belum ada aktivitas data transaksi terekam.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>
</html>