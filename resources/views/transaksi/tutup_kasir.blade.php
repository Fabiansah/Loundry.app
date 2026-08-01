<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutup Kasir - Akhir Shift</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 py-10 font-sans antialiased">
    <div class="mx-auto max-w-5xl px-4">
        <div class="mb-8 text-center">
            <h2 class="text-2xl font-black text-rose-600">Penutupan Kasir</h2>
            <p class="mt-1 text-sm text-gray-500">Harap hitung semua uang fisik di laci sebelum menutup kasir.</p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="h-fit space-y-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-md">
                <h3 class="border-b pb-3 text-base font-bold text-slate-900">Ringkasan Sesi Kas</h3>

                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-500">
                        <span>Modal Awal:</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($kasAktif->modal_awal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>(+) Penjualan Lunas:</span>
                        <span class="font-bold text-emerald-600">+ Rp {{ number_format($totalPendapatanShift, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>(-) Pengeluaran Kas:</span>
                        <span class="font-bold text-rose-600">- Rp {{ number_format($totalPengeluaranKas, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2 text-base font-black">
                        <span>Wajib di Laci:</span>
                        <span class="text-indigo-600">Rp {{ number_format($estimasiUangLaci, 0, ',', '.') }}</span>
                    </div>
                </div>

                <form action="{{ route('kasir.tutupProcess') }}" method="POST" class="space-y-4 pt-2">
                    @csrf
                    <div>
                        <label class="mb-1 block text-sm font-semibold text-gray-700">Masukkan Uang Fisik Asli (Rp)</label>
                        <input type="text" id="uang_fisik_laci_display" inputmode="numeric" autocomplete="off" class="mt-1 block w-full rounded-xl border border-gray-300 p-3 text-lg font-bold text-center shadow-sm focus:border-rose-500 focus:ring-rose-500" oninput="formatUangFisik(this)">
                        <input type="hidden" id="uang_fisik_laci" name="uang_fisik_laci" value="">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('transaksi') }}" class="w-1/3 rounded-xl bg-gray-100 py-3 text-center text-sm font-bold text-gray-700 transition hover:bg-gray-200">
                            Kembali
                        </a>
                        <button type="submit" onclick="return confirm('Sesi kasir akan diakhiri dan akun otomatis logout. Lanjutkan?')" class="w-2/3 rounded-xl bg-rose-600 px-4 py-3 text-sm font-bold tracking-wide text-white shadow transition-all hover:bg-rose-700">
                            Tutup Kasir
                        </button>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-md md:col-span-2">
                <h3 class="mb-4 flex items-center justify-between text-base font-bold text-slate-900">
                    <span>📋 Riwayat Penjualan Shift Ini</span>
                    <span class="text-xs font-normal text-slate-500">{{ $riwayatTransaksi->count() }} Nota</span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b bg-slate-50 font-bold uppercase text-slate-400">
                                <th class="p-2.5">Waktu</th>
                                <th class="p-2.5">Pelanggan</th>
                                <th class="p-2.5">Paket</th>
                                <th class="p-2.5">Nominal</th>
                                <th class="p-2.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($riwayatTransaksi as $item)
                                <tr>
                                    <td class="whitespace-nowrap p-2.5 text-slate-500">{{ $item->created_at->format('H:i') }}</td>
                                    <td class="p-2.5 font-bold text-slate-800">{{ $item->nama_pelanggan }}</td>
                                    <td class="p-2.5 text-slate-600">{{ $item->paket }} ({{ $item->berat_kg }}kg)</td>
                                    <td class="p-2.5 font-bold text-slate-900">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                                    <td class="p-2.5 text-center">
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $item->status_pembayaran == 'lunas' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ strtoupper($item->status_pembayaran) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-slate-400">Belum ada transaksi pada shift kasir ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatUangFisik(input) {
            let raw = input.value.replace(/\D/g, '');
            input.value = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            document.getElementById('uang_fisik_laci').value = raw;
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('form').addEventListener('submit', function () {
                const raw = document.getElementById('uang_fisik_laci').value.replace(/\D/g, '');
                document.getElementById('uang_fisik_laci').value = raw;
            });
        });
    </script>
</body>
</html>