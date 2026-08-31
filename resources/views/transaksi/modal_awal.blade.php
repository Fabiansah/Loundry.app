<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buka Toko - Modal Awal Kasir</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 selection:bg-indigo-500 selection:text-white">

    <div class="max-w-[380px] w-full bg-white p-7 rounded-3xl shadow-sm border border-slate-200/80 space-y-5">

        @if($errors->any())
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-2xl text-rose-600 text-xs font-semibold text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('modal.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="space-y-2.5">
                <!-- Input Box -->
                <div class="relative flex items-center bg-slate-50 border border-slate-200 rounded-2xl focus-within:border-indigo-600 focus-within:bg-white focus-within:ring-4 focus-within:ring-indigo-50 transition duration-150">
                    <span class="pl-4 text-xs font-bold text-slate-400 select-none">Rp</span>
                    <input 
                        type="text" 
                        id="modal_awal_display" 
                        inputmode="numeric" 
                        autocomplete="off" 
                        placeholder="0"
                        autofocus
                        class="w-full pr-4 py-3 bg-transparent text-center text-xl font-bold text-slate-800 placeholder:text-slate-300 focus:outline-none" 
                        oninput="formatModalAwal(this)"
                    >
                </div>
                <input type="hidden" id="modal_awal" name="modal_awal" value="0">

                <!-- Nominal Cepat (Chips) -->
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" onclick="setNominal(50000)" class="py-2 px-1 bg-slate-100/70 hover:bg-slate-200/60 active:scale-[0.98] text-slate-600 text-xs font-semibold rounded-xl transition">
                        50.000
                    </button>
                    <button type="button" onclick="setNominal(100000)" class="py-2 px-1 bg-slate-100/70 hover:bg-slate-200/60 active:scale-[0.98] text-slate-600 text-xs font-semibold rounded-xl transition">
                        100.000
                    </button>
                    <button type="button" onclick="setNominal(200000)" class="py-2 px-1 bg-slate-100/70 hover:bg-slate-200/60 active:scale-[0.98] text-slate-600 text-xs font-semibold rounded-xl transition">
                        200.000
                    </button>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button 
                type="submit" 
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-xl font-bold text-sm tracking-wide shadow transition-all"
            >
                Buka Kasir Sekarang
            </button>
        </form>

    </div>

    <script>
        function formatModalAwal(input) {
            let raw = input.value.replace(/\D/g, '');
            input.value = raw ? raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            document.getElementById('modal_awal').value = raw ? raw : '0';
        }

        function setNominal(value) {
            const inputDisplay = document.getElementById('modal_awal_display');
            inputDisplay.value = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            document.getElementById('modal_awal').value = value;
            inputDisplay.focus();
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('form').addEventListener('submit', function () {
                const raw = document.getElementById('modal_awal_display').value.replace(/\D/g, '');
                document.getElementById('modal_awal').value = raw ? raw : '0';
            });
        });
    </script>
</body>
</html> 