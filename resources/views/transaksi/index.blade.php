<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Transaksi Laundry</title>
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
            --app-bg: #f3f4f6;
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

        nav, .bg-white, .bg-gray-100, .bg-gray-50, .bg-slate-50, .bg-slate-100 {
            transition: background-color 0.25s ease, border-color 0.25s ease, color 0.25s ease;
        }

        nav {
            background-color: var(--app-surface) !important;
            border-color: var(--app-border) !important;
        }

        .bg-white { background-color: var(--app-surface) !important; }
        .bg-gray-50, .bg-slate-50 { background-color: var(--app-surface-soft) !important; }
        .bg-gray-100, .bg-slate-100 { background-color: rgba(148, 163, 184, 0.12) !important; }
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

    <!-- NAVIGASI UTAMA SERAGAM -->
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
                            <path d="M12 3v2.4M12 18.6v2.4M4.2 4.2l1.7 1.7M18.1 18.1l1.7 1.7M3 12h2.4M18.6 12H21M4.2 19.8l1.7-1.7M18.1 5.9l1.7-1.7" stroke-linecap="round" stroke-linejoin="round"></path>
                            <circle cx="12" cy="12" r="3.4"></circle>
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
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('sukses'))
                <div id="alert-sukses" class="mb-6 text-sm font-medium text-green-600 bg-green-100 p-3 rounded-lg border border-green-200 transition-opacity duration-500">
                    {{ session('sukses') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- FORM INPUT TRANSAKSI BARU -->
                <div class="bg-white p-6 rounded-lg shadow h-fit">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Input Transaksi</h3>

                    <form action="{{ route('transaksi.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pelanggan</label>
                            <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @error('nama_pelanggan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor HP</label>
                            <input type="text" name="nomor_hp" value="{{ old('nomor_hp') }}"  class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-800 mb-1">Paket Laundry</label>
                                <!-- TAMPILAN SELECT PAKET LAUNDRY DIPERCANTIK -->
                                <div class="relative">
                                    <select name="paket" required class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 pr-10 border text-sm focus:ring-1 focus:ring-black focus:border-black appearance-none">
                                        <option value="Reguler">Reguler (Rp 6.000 / Kg — 2 Hari)</option>
                                        <option value="Kilat">Kilat (Rp 10.000 / Kg — 1 Hari)</option>
                                        <option value="Super Kilat">Super Kilat (Rp 15.000 / Kg — 6 Jam)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Berat</label>
                                <div class="relative mt-1">
                                    <input type="number" step="0.1" name="berat_kg" value="{{ old('berat_kg') }}" class="block w-full rounded-xl border-slate-200 shadow-sm pr-12 pl-3 py-3 border text-sm focus:ring-1 focus:ring-black focus:border-black">
                                    <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 font-bold text-sm">Kg</span>
                                </div>
                                @error('berat_kg') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700"> Catatan</label>
                            <textarea name="catatan" rows="2"  class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-xl font-bold text-sm tracking-wide shadow transition-all">
                        Simpan Transaksi
                        </button>
                    </form>
                </div>

                <div class="bg-white p-6 rounded-lg shadow md:col-span-2 overflow-x-auto">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Cucian</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelanggan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bayar</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($transaksis as $item)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $item->nama_pelanggan }} 
                                        <span class="text-xs text-gray-500 block font-mono">
                                            @if(!empty($item->nomor_hp))
                                                @php
                                                    $cleanHp = preg_replace('/[^0-9]/', '', $item->nomor_hp);
                                                    $cleanHp = preg_replace('/^0/', '62', $cleanHp);
                                                    $formatted = preg_replace('/(\d{2})(\d{3,4})(\d{4})(\d+)/', '+$1 $2-$3-$4', $cleanHp);
                                                @endphp
                                                {{ $formatted }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                        
                                        <span class="mt-1 inline-block text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-indigo-100 text-indigo-800">
                                            {{ $item->paket ?? 'Reguler' }} (Rp {{ number_format($item->harga_per_kg ?? 6000, 0, ',', '.') }}/Kg)
                                        </span>

                                        @if($item->catatan)
                                            <span class="text-xs text-amber-600 block italic mt-1">Ket: {{ $item->catatan }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->berat_kg }} Kg</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        @php
                                            $statusOtomatis = $item->status_laundry_otomatis ?? $item->status_laundry;
                                        @endphp

                                        @if($statusOtomatis == 'antrean')
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-amber-100 text-amber-800">
                                                Antrean
                                            </span>
                                        @elseif($statusOtomatis == 'proses')
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-100 text-blue-800 animate-pulse">
                                                Proses Cuci
                                            </span>
                                        @elseif($statusOtomatis == 'selesai')
                                            <div class="flex items-center gap-1.5">
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-emerald-100 text-emerald-800">
                                                    Selesai
                                                </span>
                                                <form action="{{ route('transaksi.updateStatus', [$item->id, 'diambil']) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" onclick="return" class="p-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[10px] font-bold transition">
                                                        Diambil
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-slate-100 text-slate-600">
                                                Sudah Diambil
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        @if($item->status_pembayaran == 'belum_bayar')
                                            <form action="{{ route('transaksi.updatePembayaran', $item->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" onclick="return" class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full text-red-800 hover:bg-red-200">
                                                    🔴
                                                </button>
                                            </form>
                                        @else
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full text-emerald-800">
                                                    🟢 
                                                </span>
                                                <a href="{{ route('transaksi.print', ['id' => $item->id, 'auto' => 'true']) }}" target="_blank" title="Cetak Nota" class="p-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition text-xs flex items-center">
                                                    Cetak Struk
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500 italic">Belum ada transaksi hari ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>

    <!-- JAVASCRIPT MEMATIKAN NOTIFIKASI DALAM 3 DETIK -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const alertSukses = document.getElementById('alert-sukses');
            if (alertSukses) {
                setTimeout(function () {
                    alertSukses.style.opacity = '0';
                    setTimeout(function () {
                        alertSukses.style.display = 'none';
                    }, 500);
                }, 3000);
            }
        });
    </script>

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