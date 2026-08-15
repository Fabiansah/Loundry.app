<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pegawai Kasir</title>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        
        <!-- Notifikasi Banner Undangan WhatsApp -->
        @if(session('wa_link'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                <div>
                    <h4 class="text-sm font-bold text-emerald-800">Undangan Akun Berhasil Dibuat!</h4>
                    <p class="text-xs text-emerald-600 mt-0.5">Klik tombol di samping untuk langsung mengirimkan tautan aktivasi ke WhatsApp <b>{{ session('kasir_name') }}</b>.</p>
                </div>
                <a href="{{ session('wa_link') }}" target="_blank" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-2 flex-shrink-0">
                    <span>Buka WhatsApp</span>
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.316 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.818-.981z"/></svg>
                </a>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Form Undang Pegawai -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
                <h3 class="text-lg font-bold text-slate-900 mb-1">Undang Kasir Baru</h3>
                <p class="text-xs text-slate-400 mb-4">Kasir akan membuat password mandiri via tautan aktivasi WhatsApp.</p>

                @if (session('sukses') && !session('wa_link'))
                    <div id="alert-sukses" class="mb-4 text-xs font-bold text-emerald-700 bg-emerald-50 p-3 rounded-xl border border-emerald-200">
                        {{ session('sukses') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 text-xs font-bold text-rose-700 bg-rose-50 p-3 rounded-xl border border-rose-200">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('pegawai.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-500">Nama Lengkap</label>
                        <input type="text" name="name" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('name') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500">Email</label>
                        <input type="email" name="email" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('email') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500">Nomor WhatsApp</label>
                        <input type="text" name="no_hp" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('no_hp') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-xl font-bold text-sm tracking-wide shadow transition-all flex items-center justify-center gap-2">
                        <span>Kirim Undangan Aktivasi</span>
                    </button>
                </form>
            </div>

            <!-- Tabel Daftar Pegawai -->
            <div class="lg:col-span-2 bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900">Daftar Pegawai Kasir</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Daftar seluruh pegawai kasir beserta status aktivasi akunnya.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Nama Lengkap</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">WhatsApp</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-400 uppercase w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse ($pegawais as $pegawai)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900">
                                        {{ $pegawai->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                        <div>{{ $pegawai->email }}</div>
                                        <div class="text-[11px] text-slate-400 font-medium font-mono">
                                            @if($pegawai->no_hp)
                                                @php
                                                    $hp = preg_replace('/^0/', '62', $pegawai->no_hp);
                                                    $formatted = preg_replace('/(\d{2})(\d{3,4})(\d{4})(\d+)/', '+$1 $2-$3-$4', $hp);
                                                @endphp
                                                {{ $formatted }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(($pegawai->status_akun ?? 'aktif') === 'aktif')
                                            <span class="px-2.5 py-1 inline-flex text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 inline-flex text-[10px] font-bold rounded-full bg-amber-50 text-amber-700 border border-amber-200 uppercase tracking-wider">
                                                Menunggu Aktivasi
                                            </span>
                                        @endif
                                        
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pegawai ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-800 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">Belum ada pegawai kasir yang didaftarkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Auto-Dismiss Alert
        const alertPegawai = document.getElementById('alert-pegawai') || document.getElementById('alert-sukses');
        if (alertPegawai) {
            alertPegawai.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            setTimeout(() => {
                alertPegawai.style.opacity = '0';
                alertPegawai.style.transform = 'translateY(-8px)';
                setTimeout(() => alertPegawai.remove(), 500);
            }, 3000);
        }

        // 2. Dark/Light Theme Switcher
        const toggle = document.getElementById('theme-toggle');
        const icon = document.getElementById('theme-toggle-icon');
        const label = document.getElementById('theme-toggle-label');

        const icons = {
            dark: '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4a8.5 8.5 0 1 0 10.5 10.5Z" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>',
            light: '<path d="M12 3v2.4M12 18.6v2.4M4.2 4.2l1.7 1.7M18.1 18.1l1.7 1.7M3 12h2.4M18.6 12H21M4.2 19.8l1.7-1.7M18.1 5.9l1.7-1.7" stroke-linecap="round" stroke-linejoin="round"></path><circle cx="12" cy="12" r="3.4"></circle>'
        };

        const updateTheme = (theme) => {
            document.documentElement.setAttribute('data-theme', theme);
            document.documentElement.classList.toggle('dark', theme === 'dark');
            localStorage.setItem('theme', theme);

            if (icon) icon.innerHTML = icons[theme] || icons.light;
            if (label) label.textContent = theme === 'dark' ? 'Mode Gelap' : 'Mode Terang';
        };

        // Inisialisasi tema saat pertama dimuat
        const savedTheme = localStorage.getItem('theme') || document.documentElement.getAttribute('data-theme') || 'light';
        updateTheme(savedTheme);

        // Event listener toggle tema
        toggle?.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            updateTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });
    });
</script>
</body>
</html>