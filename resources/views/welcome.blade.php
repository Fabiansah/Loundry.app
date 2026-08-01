<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Laundry UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <div class="max-w-md w-full bg-white p-8 rounded-xl shadow-lg text-center">
            
            <div class="inline-flex p-3 bg-indigo-100 text-indigo-600 rounded-full mb-4">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Aplikasi Laundry UMKM</h1>
            <p class="text-sm text-gray-600 mb-8">Selamat datang! Silakan masuk ke akun kasir Anda untuk mulai mengelola transaksi cucian pelanggan.</p>

            <div class="space-y-3">
                <a href="{{ route('login') }}" class="block w-full bg-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-indigo-700 transition shadow">
                    Masuk (Log In)
                </a>

                <a href="{{ route('register') }}" class="block w-full bg-gray-200 text-gray-800 font-semibold py-3 px-4 rounded-lg hover:bg-gray-300 transition">
                    Daftar Akun Baru (Register)
                </a>

                <hr class="my-4 border-gray-200">

                <a href="{{ url('/transaksi') }}" class="block w-full bg-emerald-600 text-white font-semibold py-3 px-4 rounded-lg hover:bg-emerald-700 transition shadow">
                    Buka Kelola Transaksi
                </a>
            </div>

        </div>
        <p class="text-xs text-gray-500 mt-6">&copy; Aplikasi Laundry Project</p>
    </div>
</body>
</html>