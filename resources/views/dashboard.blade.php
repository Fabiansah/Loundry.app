<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Laundry UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            --chart-grid: #f1f5f9;
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
            --chart-grid: #1e293b;
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
                            <rect x="4" y="2" width="16" height="20" rx="3" ry="3"></rect>
                            <line x1="8" y1="6" x2="10" y2="6" stroke-linecap="round"></line>
                            <circle cx="16" cy="6" r="1" fill="currentColor"></circle>
                            <circle cx="12" cy="14" r="5"></circle>
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- HEADER SECTION + TOMBOL CLOSING -->
<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200/60 pb-6">
                <div class="space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <!-- Indikator Shift Ditempatkan Sebelum Judul -->
                        @if(isset($shiftAktif) && $shiftAktif)
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-emerald-50 rounded-full border border-emerald-200 shadow-sm" title="Shift Kasir Aktif">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center w-6 h-6 bg-slate-100 rounded-full border border-slate-200 shadow-sm" title="Kasir Closing (Shift Tutup)">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            </span>
                        @endif

                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Overview Operasional</h1>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500">Pantau performa keuangan dan antrean produksi operasional laundry secara real-time.</p>
                </div>

                <!-- Tombol Tutup Shift (Hanya Muncul untuk Kasir saat Shift Aktif) -->
                @if(auth()->user()->role === 'kasir' && isset($shiftAktif) && $shiftAktif)
                    <div>
                        <a href="{{ route('kasir.tutupForm') }}" 
                           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full border border-slate-200 bg-white/80 hover:bg-rose-50/80 hover:border-rose-200 text-slate-700 hover:text-rose-600 text-xs font-semibold shadow-sm transition-all duration-200 active:scale-95">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                            </span>
                            <span>Tutup Shift</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- GRID KARTU METRIK UTAMA (SHIFT AKTIF) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
                
                <!-- KARTU 1: PENDAPATAN LUNAS -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pendapatan Lunas</span>
                        @if(auth()->user()->role == 'admin')
                            <h3 class="text-xl font-black text-indigo-600 mt-1 font-mono">Rp {{ number_format($pendapatanLunas ?? 0, 0, ',', '.') }}</h3>
                        @else
                            <h3 class="text-xs font-bold text-slate-500 mt-3 flex items-center gap-1">Khusus Admin</h3>
                        @endif
                    </div>
                </div>

                <!-- KARTU 2: TOTAL PIUTANG / BELUM TERBAYAR -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Belum Terbayar</span>
                        @if(auth()->user()->role == 'admin')
                            <h3 class="text-xl font-black text-indigo-600 mt-1 font-mono">Rp {{ number_format($belumTerbayar ?? 0, 0, ',', '.') }}</h3>
                        @else
                            <h3 class="text-xs font-bold text-slate-500 mt-3 flex items-center gap-1">Khusus Admin</h3>
                        @endif
                    </div>
                </div>

                <!-- KARTU 3: CUCIAN AKTIF -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Dalam Proses Cuci</span>
                        <h3 class="text-xl font-black text-indigo-600 mt-1 font-mono">{{ $dalamProsesCuci ?? 0 }} Nota</h3>
                    </div>
                </div>

                <!-- KARTU 4: BEBAN BERAT CUCIAN -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Beban Mesin Aktif</span>
                        <h3 class="text-xl font-black text-indigo-600 mt-1 font-mono">{{ $bebanMesinAktif ?? 0 }} Kg</h3>
                    </div>
                </div>

                <!-- KARTU 5: SIAP DIAMBIL -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Siap Diambil</span>
                        <h3 class="text-xl font-black text-indigo-600 mt-1 font-mono">{{ $siapDiambil ?? 0 }} Nota</h3>
                    </div>
                </div>

            </div>

            <!-- GRAFIK TREN PENDAPATAN BULANAN (MODERN SAAS STYLE) -->
            @if(auth()->user()->role == 'admin')
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-100 shadow-sm space-y-6">
                <!-- Header Card & Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <h2 class="text-base font-bold text-slate-900 tracking-tight">Fluktuasi Pendapatan</h2>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            Total omzet periode terpilih: <strong class="text-indigo-600 font-mono font-bold text-sm ml-1">Rp {{ number_format($totalOmzetBulanIni ?? 0, 0, ',', '.') }}</strong>
                        </p>
                    </div>

                    <!-- Filter Dropdown Minimalis -->
                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <div class="relative">
                            <select name="bulan" onchange="this.form.submit()" class="text-xs font-semibold bg-slate-50 hover:bg-slate-100/80 border border-slate-200/80 text-slate-700 rounded-xl pl-3 pr-8 py-2 outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer appearance-none">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ ($selectedMonth ?? \Carbon\Carbon::now()->month) == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>

                        <div class="relative">
                            <select name="tahun" onchange="this.form.submit()" class="text-xs font-semibold bg-slate-50 hover:bg-slate-100/80 border border-slate-200/80 text-slate-700 rounded-xl pl-3 pr-8 py-2 outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all cursor-pointer appearance-none">
                                @php
                                    $tahunSekarang = now()->year;
                                    $tahunAwal     = 2025;
                                    $tahunAkhir    = $tahunSekarang + 5;
                                @endphp
                                @for($y = $tahunAwal; $y <= $tahunAkhir; $y++)
                                    <option value="{{ $y }}" {{ ($selectedYear ?? $tahunSekarang) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Canvas Grafik -->
                <div class="relative h-[300px] w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            @endif

            <!-- SEKSI DAFTAR RIWAYAT TRANSAKSI -->
            <div class="bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Aktivitas Transaksi Terkini</h2>
                        @php
                            $listTransaksi = $transaksiTerkini ?? $riwayatTerbaru ?? collect();
                            $countTransaksi = $listTransaksi->count();
                        @endphp
                        @if($countTransaksi == 0)
                            <p class="text-xs text-slate-400 mt-0.5">Tidak ada riwayat pemesanan laundry dalam 7 hari terakhir.</p>
                        @elseif($countTransaksi == 1)
                            <p class="text-xs text-slate-400 mt-0.5">Menampilkan 1 transaksi dari kasir dalam 7 hari terakhir.</p>
                        @else
                            <p class="text-xs text-slate-400 mt-0.5">Menampilkan {{ $countTransaksi }} transaksi dari kasir dalam 7 hari terakhir.</p>
                        @endif
                    </div>
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
                            @forelse ($listTransaksi as $item)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="block font-bold text-slate-900">{{ $item->nama_pelanggan }}</span>
                                        <span class="text-xs text-slate-400 block mt-0.5 font-mono">
                                            @if(!empty($item->nomor_hp))
                                                @php
                                                    $cleanHp = preg_replace('/[^0-9]/', '', $item->nomor_hp);
                                                    $cleanHp = preg_replace('/^0/', '62', $cleanHp);
                                                    $formatted = preg_replace('/(\d{2})(\d{3,4})(\d{4})(\d+)/', '+$1 $2-$3-$4', $cleanHp);
                                                @endphp
                                                {{ $formatted }}
                                            @else
                                                Tanpa No. HP
                                            @endif
                                        </span>
                                        <span class="mt-1 inline-block text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ $item->paket ?? 'Reguler' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600 font-semibold">{{ $item->berat_kg }} Kg</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-900 font-extrabold font-mono">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $stLaundry = strtolower($item->status_laundry ?? $item->status_pesanan ?? 'antrean');
                                        @endphp
                                        <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full 
                                            {{ in_array($stLaundry, ['antrean', 'antre']) ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                            {{ in_array($stLaundry, ['proses', 'cuci']) ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                                            {{ $stLaundry == 'selesai' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                            {{ in_array($stLaundry, ['diambil', 'siap_ambil']) ? 'bg-slate-100 text-slate-600' : '' }}
                                        ">
                                            {{ ucfirst($stLaundry) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full 
                                            {{ strtolower($item->status_pembayaran) == 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}
                                        ">
                                            {{ strtolower($item->status_pembayaran) == 'lunas' ? 'Lunas' : 'Belum Bayar' }}
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
    // Inisialisasi Chart Fluktuasi Pendapatan (Modern SaaS Theme)
    @if(auth()->user()->role == 'admin')
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        const chartContext = ctx.getContext('2d');

        // Gradient Halus Area Bawah Kurva
        const gradient = chartContext.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.28)'); // Indigo soft
        gradient.addColorStop(0.6, 'rgba(99, 102, 241, 0.04)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        new Chart(chartContext, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_map(fn($l) => str_replace('Tgl ', '', $l), $chartLabels ?? [])) !!},
                datasets: [{
                    label: 'Pendapatan',
                    data: {!! json_encode($chartData ?? []) !!},
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    cubicInterpolationMode: 'monotone',
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#ffffff',
                    pointHoverBackgroundColor: '#6366f1',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2.5,
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#94a3b8',
                        titleFont: { size: 11, weight: '600', family: 'sans-serif' },
                        bodyColor: '#ffffff',
                        bodyFont: { size: 13, weight: '800', family: 'monospace' },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            title: function(items) {
                                return 'Tanggal ' + items[0].label;
                            },
                            label: function(context) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        border: { dash: [5, 5], display: false },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.1)',
                            drawTicks: false,
                        },
                        ticks: {
                            padding: 10,
                            color: '#94a3b8',
                            font: { size: 11, weight: '600' },
                            callback: function(value) {
                                if (value === 0) return 'Rp 0';
                                return 'Rp ' + (value / 1000) + 'k';
                            }
                        }
                    },
                    x: {
                        border: { display: false },
                        grid: { display: false },
                        ticks: {
                            padding: 8,
                            color: '#94a3b8',
                            font: { size: 10, weight: '600' },
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 16
                        }
                    }
                }
            }
        });
    }
    @endif

    // Script Toggle Mode Gelap / Terang
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('theme-toggle');
        const icon = document.getElementById('theme-toggle-icon');

        const updateTheme = function (theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('theme', theme);
            if (icon) {
                icon.innerHTML = theme === 'dark'
                    ? '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>'
                    : '<path d="M12 3v2.4M12 18.6v2.4M4.2 4.2l1.7 1.7M18.1 18.1l1.7 1.7M3 12h2.4M18.6 12H21M4.2 19.8l1.7-1.7M18.1 5.9l1.7-1.7" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="12" cy="12" r="3.4"></circle>';
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