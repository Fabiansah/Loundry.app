<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pegawai Kasir</title>
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


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
                <h3 class="text-lg font-bold text-slate-900 mb-2">Daftarkan Pegawai</h3>
                <p class="text-xs text-slate-400 mb-4">Akun yang dibuat otomatis memiliki hak akses sebagai Kasir.</p>

                    @if (session('sukses'))
                        <div class="mb-4 text-xs font-bold text-emerald-700 bg-emerald-50 p-3 rounded-xl border border-emerald-200">
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
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500">Email</label>
                        <input type="email" name="email" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('email') <span class="text-rose-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500">Password</label>
                        <input type="password" name="password" required placeholder="Minimal 8 karakter" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-xl font-bold text-sm tracking-wide shadow transition-all">
                        Simpan & Aktifkan Akun
                    </button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900">Daftar Pegawai Aktif</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Berikut adalah daftar nama-nama pegawai yang berhak mengoperasikan kasir laundry.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Nama Pegawai</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Email Login</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-slate-400 uppercase">Role System</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-slate-400 uppercase w-24">Aksi</th> </t>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse ($pegawais as $pegawai)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap font-bold text-slate-900">{{ $pegawai->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-slate-600">{{ $pegawai->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-[10px] font-black rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100 uppercase tracking-wider">
                                            {{ $pegawai->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <form action="{{ route('pegawai.destroy', $pegawai->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun pegawai ini? Mereka akan langsung dikeluarkan dan tidak bisa login kembali!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-700 p-1.5 bg-rose-50 hover:bg-rose-100 rounded-lg transition text-xs font-bold">
                                                Hapus Akun
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

</body>
</html>