<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengeluaran Kas - Laundry UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Mengunci scrollbar vertikal agar navbar tidak bergeser antar halaman */
        html {
            overflow-y: scroll;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased">

    <!-- NAVIGASI UTAMA SERAGAM -->
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

            <!-- 2 KARTU RINGKASAN PEMBUKUAN (MURNI MODAL AWAL & PENGELUARAN) -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-emerald-700">Modal Awal Kasir Shift Ini</p>
                    <p class="mt-2 text-2xl font-black text-emerald-700">Rp {{ number_format($modalKasir ?? 0, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-emerald-600">Nominal uang kas awal laci sesuai input pembukaan kasir.</p>
                </div>

                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-rose-700">Pengeluaran Kas Shift Ini</p>
                    <p class="mt-2 text-2xl font-black text-rose-700">Rp {{ number_format($pengeluaranHariIni ?? 0, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-rose-600">Total penggunaan uang kas mendadak pada shift aktif.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- FORM INPUT PENGELUARAN MENDESAK -->
                <div class="bg-white p-6 rounded-lg shadow h-fit">
                    <h3 class="text-lg font-medium text-gray-900 mb-1 flex items-center gap-2">
                        Pengeluaran Kas
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">Mencatat penggunaan uang di laci kasir untuk kebutuhan operasional mendadak.</p>

                    <form action="{{ route('pengeluaran.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <input type="text" name="keterangan" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 tracking-wider mb-1.5">Nominal Pengeluaran</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 font-bold text-sm">Rp</span>
                                <input type="text" id="jumlah_display" inputmode="numeric" class="w-full rounded-xl border-slate-200 text-slate-900 bg-slate-50/50 pl-10 pr-3.5 py-3 border text-sm font-black focus:bg-white focus:ring-1 focus:ring-black focus:border-black transition-all outline-none">
                            </div>
                            <input type="hidden" name="jumlah" id="jumlah" value="">
                        </div>

                        <button type="submit" onclick="return" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-xl font-bold text-sm tracking-wide shadow transition-all">
                            Simpan Pengeluaran
                        </button>
                    </form>
                </div>

                <!-- TABEL DAFTAR PENGELUARAN SHIFT INI -->
                <div class="bg-white p-6 rounded-lg shadow md:col-span-2 overflow-x-auto">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Riwayat Pengeluaran Kas Shift Ini</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Nominal Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($pengeluarans as $item)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $item->created_at->format('H:i') }} WIB
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ $item->keterangan }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-bold text-red-600 text-right">
                                        - Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">Belum ada pengeluaran kas mendadak pada shift ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>

    <!-- JAVASCRIPT MEMATIKAN NOTIFIKASI & FORMAT RUPIAH -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto hide notification 3 detik
            const alertSukses = document.getElementById('alert-sukses');
            if (alertSukses) {
                setTimeout(function () {
                    alertSukses.style.opacity = '0';
                    setTimeout(function () {
                        alertSukses.style.display = 'none';
                    }, 500);
                }, 3000);
            }

            // Real-time Rupiah Formatter
            const jumlahDisplay = document.getElementById('jumlah_display');
            const jumlahHidden = document.getElementById('jumlah');

            if (jumlahDisplay && jumlahHidden) {
                jumlahDisplay.addEventListener('input', function () {
                    const angka = this.value.replace(/\D/g, '');
                    if (!angka) {
                        jumlahHidden.value = '';
                        this.value = '';
                        return;
                    }

                    jumlahHidden.value = angka;
                    this.value = Number(angka).toLocaleString('id-ID');
                });
            }
        });
    </script>

</body>
</html>