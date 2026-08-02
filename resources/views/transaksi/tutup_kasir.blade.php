<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penutupan Kasir (End of Shift)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 py-8 px-4 antialiased">
    <div class="mx-auto max-w-6xl">
        
        <!-- Header SOP Info Kasir (Rata Tengah: Waktu Buka - Kasir - Waktu Tutup) -->
        <div class="mb-6 flex justify-center border-b border-slate-200 pb-5">
            <div class="grid grid-cols-3 divide-x divide-slate-200 bg-white p-3 rounded-xl border border-slate-200 shadow-sm text-xs gap-3 w-full max-w-xl text-center">
                <!-- 1. Waktu Buka -->
                <div class="px-2">
                    <p class="text-slate-400 font-medium">Waktu Buka</p>
                    <p class="font-bold text-slate-800">{{ $kasAktif->created_at ? $kasAktif->created_at->format('d M Y, H:i') : '-' }}</p>
                </div>
                
                <!-- 2. Kasir (Tengah) -->
                <div class="px-2">
                    <p class="text-slate-400 font-medium">Kasir</p>
                    <p class="font-bold text-slate-800 truncate">{{ $kasAktif->user->name ?? Auth::user()->name }}</p>
                </div>

                <!-- 3. Waktu Tutup -->
                <div class="px-2">
                    <p class="text-slate-400 font-medium">Waktu Tutup</p>
                    <p class="font-bold text-rose-600">{{ date('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            
            <!-- Left Column: Form & Rekap Kas -->
            <div class="lg:col-span-5 space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4 pb-2 border-b">RINGKASAN UANG SHIFT</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Modal Awal</span>
                            <span class="font-semibold text-slate-900">Rp {{ number_format($kasAktif->modal_awal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Penjualan</span>
                            <span class="font-semibold text-emerald-600">+ Rp {{ number_format($totalPendapatanShift, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600">
                            <span>Pengeluaran</span>
                            <span class="font-semibold text-rose-600">- Rp {{ number_format($totalPengeluaranKas, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="my-2 border-t border-dashed border-slate-200 pt-3 flex justify-between items-center">
                            <span class="font-bold text-slate-900">Nominal Uang</span>
                            <span class="text-base font-bold text-indigo-600">Rp {{ number_format($estimasiUangLaci, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Input Real-time Balance Form -->
                    <form action="{{ route('kasir.tutupProcess') }}" method="POST" class="mt-6 space-y-4">
                        @csrf
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">

                            <!-- Group Input dengan Prefix Rp di Depan -->
                            <div class="relative flex items-center rounded-lg border border-slate-300 bg-white shadow-sm focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 transition">
                                <span class="pl-3.5 pr-1 text-base font-extrabold text-slate-400 select-none">
                                    Rp
                                </span>
                                <input 
                                    type="text" 
                                    id="uang_fisik_laci_display" 
                                    inputmode="numeric" 
                                    autocomplete="off"
                                    placeholder="0"
                                    class="w-full bg-transparent p-3 pl-1 text-xl font-extrabold text-slate-900 focus:outline-none"
                                    oninput="formatUangFisik(this)"
                                >
                            </div>
                            <input type="hidden" id="uang_fisik_laci" name="uang_fisik_laci" value="">

                            <!-- Real-time Status Selisih -->
                            <div id="selisih_wrapper" class="mt-3 hidden p-2.5 rounded-lg text-xs font-semibold text-center border">
                                <span id="selisih_text"></span>
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <a href="{{ route('transaksi') }}" class="w-1/3 py-3 rounded-xl border border-slate-200 bg-white text-center text-xs font-bold text-slate-600 transition hover:bg-slate-50">
                                Batal
                            </a>
                            <button type="submit" onclick="return" class="w-2/3 py-3 rounded-xl bg-rose-600 text-xs font-bold text-white shadow-md transition hover:bg-rose-700">
                                Close Shift
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Audit Trail & Transaction Log -->
            <div class="lg:col-span-7 space-y-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between mb-4 pb-2 border-b">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">
                            RINGKASAN TRANSAKSI SHIFT
                        </h2>
                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                            {{ $riwayatTransaksi->count() }} Transaksi
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 font-semibold uppercase text-slate-400 border-b">
                                    <th class="py-2.5 px-3">Jam</th>
                                    <th class="py-2.5 px-3">Pelanggan</th>
                                    <th class="py-2.5 px-3">Paket Laundry</th>
                                    <th class="py-2.5 px-3 text-right">Total</th>
                                    <th class="py-2.5 px-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($riwayatTransaksi as $item)
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-3 px-3 text-slate-500 font-mono">{{ $item->created_at->format('H:i') }}</td>
                                        <td class="py-3 px-3 font-semibold text-slate-800">{{ $item->nama_pelanggan }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $item->paket }} <span class="text-slate-400">({{ $item->berat_kg }}kg)</span></td>
                                        <td class="py-3 px-3 font-bold text-slate-900 text-right">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="inline-block rounded-md px-2 py-0.5 text-[10px] font-bold tracking-wide {{ $item->status_pembayaran == 'lunas' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : 'bg-amber-50 text-amber-600 border border-amber-200' }}">
                                                {{ strtoupper($item->status_pembayaran) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400">Belum ada transaksi terekam pada shift ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- JS Logic untuk Format & SOP Selisih Fisik -->
    <script>
        const estimasiLaci = {{ $estimasiUangLaci }};

        function formatUangFisik(input) {
            let raw = input.value.replace(/\D/g, '');
            input.value = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
            document.getElementById('uang_fisik_laci').value = raw;

            // Hitung Selisih Real-Time
            const wrapper = document.getElementById('selisih_wrapper');
            const text = document.getElementById('selisih_text');
            
            if (raw === '') {
                wrapper.classList.add('hidden');
                return;
            }

            wrapper.classList.remove('hidden');
            const inputVal = parseInt(raw, 10);
            const diff = inputVal - estimasiLaci;

            if (diff === 0) {
                wrapper.className = "mt-3 p-2.5 rounded-lg text-xs font-semibold text-center bg-emerald-50 text-emerald-700 border border-emerald-200";
                text.innerText = "Balance";
            } else if (diff > 0) {
                wrapper.className = "mt-3 p-2.5 rounded-lg text-xs font-semibold text-center bg-indigo-50 text-indigo-700 border border-indigo-200";
                text.innerText = "▲ Surplus: + Rp " + new Intl.NumberFormat('id-ID').format(diff);
            } else {
                wrapper.className = "mt-3 p-2.5 rounded-lg text-xs font-semibold text-center bg-rose-50 text-rose-700 border border-rose-200";
                text.innerText = "▼ Defisit: - Rp " + new Intl.NumberFormat('id-ID').format(Math.abs(diff));
            }
        }
    </script>
</body>
</html>