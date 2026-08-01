<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Aplikasi Laundry</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-100 px-4 py-8 flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-xl">
            <div class="mb-6 flex items-center justify-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-indigo-100 bg-indigo-50 text-indigo-600 shadow-sm">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="2" width="16" height="20" rx="3" ry="3"></rect>
                        <line x1="8" y1="6" x2="10" y2="6" stroke-linecap="round"></line>
                        <circle cx="16" cy="6" r="1" fill="currentColor"></circle>
                        <circle cx="12" cy="14" r="5"></circle>
                        <path d="M10 13c1-1 3-1 4 0s3 1 4 0" stroke-linecap="round"></path>
                    </svg>
                </div>
            </div>

            <h2 class="text-center text-xl font-semibold text-gray-900">Masuk Aplikasi Laundry</h2>
            <p class="mt-2 text-center text-sm text-gray-500">Silakan masuk untuk mengelola transaksi.</p>

            @if ($errors->any())
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm font-medium text-red-600">
                    Nama pengguna atau password salah.
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Pengguna</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" required class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm p-3 border text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="h-4 w-4 rounded border-gray-300 text-indigo-600">
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat Saya</label>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-xl font-bold text-sm tracking-wide shadow transition-all">
                    Masuk Sekarang
                </button>
            </form>

            <p class="mt-5 text-center text-xs italic text-gray-400">
                Pegawai baru? Hubungi Admin/Pemilik Toko untuk pembuatan akun kasir.
            </p>
        </div>
    </div>
</body>
</html>