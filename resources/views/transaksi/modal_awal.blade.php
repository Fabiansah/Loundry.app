<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buka Toko - Modal Awal Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 font-sans h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-md border border-gray-200">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-black text-indigo-600">Pembukaan Kasir</h2>
            <p class="text-sm text-gray-500 mt-1">Silakan masukkan nominal uang modal awal di laci kasir untuk memulai transaksi hari ini.</p>
        </div>

        <form action="{{ route('modal.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700">Jumlah Uang Modal (Rp)</label>
                <input type="text" id="modal_awal_display" inputmode="numeric" autocomplete="off" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm p-3 border text-lg font-bold text-center focus:ring-indigo-500 focus:border-indigo-500" oninput="formatModalAwal(this)">
                <input type="hidden" id="modal_awal" name="modal_awal" value="">
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-xl font-bold tracking-wide shadow transition-all">
                Buka Kasir
            </button>
        </form>
    </div>

    <script>
        function formatModalAwal(input) {
            let raw = input.value.replace(/\D/g, '');
            input.value = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            document.getElementById('modal_awal').value = raw;
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('form').addEventListener('submit', function () {
                const raw = document.getElementById('modal_awal').value.replace(/\D/g, '');
                document.getElementById('modal_awal').value = raw;
            });
        });
    </script>
</body>
</html>