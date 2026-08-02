<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Laundry UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <style>
        /* Mengunci scrollbar vertikal agar navbar tidak bergeser antar halaman */
        html {
            overflow-y: scroll;
        }

        :root {
            color-scheme: light;
            --app-bg: #f8fafc;
            --app-surface: #ffffff;
            --app-surface-soft: #f8fafc;
            --app-text: #0f172a;
            --app-muted: #64748b;
            --app-border: #e2e8f0;
            --app-input-bg: #f8fafc;
            --app-shadow: 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        [data-theme="dark"] {
            color-scheme: dark;
            --app-bg: #020617;
            --app-surface: #111827;
            --app-surface-soft: #0f172a;
            --app-text: #f8fafc;
            --app-muted: #94a3b8;
            --app-border: #334155;
            --app-input-bg: #0f172a;
            --app-shadow: 0 10px 25px rgba(2, 6, 23, 0.35);
        }

        body {
            background-color: var(--app-bg);
            color: var(--app-text);
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        nav, .bg-white, .bg-slate-50, .bg-gray-50, .bg-slate-100, .bg-gray-100 {
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.25s ease;
        }

        nav {
            background-color: var(--app-surface) !important;
            border-color: var(--app-border) !important;
        }

        .bg-white { background-color: var(--app-surface) !important; }
        .bg-slate-50, .bg-gray-50 { background-color: var(--app-surface-soft) !important; }
        .bg-slate-100, .bg-gray-100 { background-color: rgba(148, 163, 184, 0.12) !important; }
        .text-slate-900, .text-gray-900, .text-slate-800, .text-gray-800 { color: var(--app-text) !important; }
        .text-slate-500, .text-gray-500, .text-slate-600, .text-gray-600, .text-slate-400, .text-gray-400 { color: var(--app-muted) !important; }
        .border-slate-100, .border-slate-200, .border-gray-100, .border-gray-200 { border-color: var(--app-border) !important; }
        .shadow, .shadow-sm { box-shadow: var(--app-shadow) !important; }
        input, textarea, select {
            background-color: var(--app-input-bg) !important;
            border-color: var(--app-border) !important;
            color: var(--app-text) !important;
        }
        input::placeholder, textarea::placeholder { color: var(--app-muted) !important; }
        table thead { background-color: var(--app-surface-soft) !important; }
        table tbody tr { background-color: var(--app-surface) !important; }
        table tbody tr:hover { background-color: rgba(79, 70, 229, 0.08) !important; }
        .theme-toggle-icon { transition: transform 0.2s ease; }
        .theme-toggle-btn:hover .theme-toggle-icon { transform: rotate(20deg); }
    </style>
</head>
<body class="font-sans antialiased">

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
                <button id="theme-toggle" type="button" class="theme-toggle-btn inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-3 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg id="theme-toggle-icon" class="theme-toggle-icon h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 3v2m0 14v2m9-9h-2M5 12H3m9-6.5L10.5 8m3 8l-1.5-1.5M8.5 8 7 6.5m10 10-1.5-1.5M8.5 16 7 17.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <circle cx="12" cy="12" r="3.5"></circle>
                    </svg>
                </button>
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
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Progres</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('theme-toggle');
        const icon = document.getElementById('theme-toggle-icon');
        const label = document.getElementById('theme-toggle-label');

        const updateTheme = function (theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            if (icon) {
                icon.innerHTML = theme === 'dark'
                    ? '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>'
                    : '<path d="M12 3v2.4M12 18.6v2.4M4.2 4.2l1.7 1.7M18.1 18.1l1.7 1.7M3 12h2.4M18.6 12H21M4.2 19.8l1.7-1.7M18.1 5.9l1.7-1.7" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="12" cy="12" r="3.4"></circle>';
            }
            if (label) {
                label.textContent = theme === 'dark' ? 'Mode Gelap' : 'Mode Terang';
            }
        };

        const initialTheme = document.documentElement.getAttribute('data-theme') || 'light';
        updateTheme(initialTheme);

        toggle?.addEventListener('click', function () {
            const nextTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            updateTheme(nextTheme);
        });
    });
</script>
</body>
</html>