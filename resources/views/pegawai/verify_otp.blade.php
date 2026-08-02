<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi OTP Pegawai</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white p-6 rounded-2xl border border-slate-200 shadow-sm text-center space-y-4">
        <h2 class="text-xl font-bold text-slate-900">Verifikasi OTP</h2>
        <p class="text-xs text-slate-500">Masukkan 6 digit kode unik yang telah dikirim ke <br><b class="text-slate-800">{{ $email }}</b></p>

        @if($errors->has('otp'))
            <div class="p-2.5 bg-rose-50 text-rose-600 text-xs font-semibold rounded-lg">
                {{ $errors->first('otp') }}
            </div>
        @endif

        <form action="{{ route('pegawai.otp.verify') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            
            <input 
                type="text" 
                name="otp" 
                maxlength="6" 
                inputmode="numeric" 
                placeholder="000000" 
                class="w-full text-center text-2xl font-bold tracking-widest p-3 border border-slate-300 rounded-xl focus:border-indigo-500 focus:outline-none"
                required
            >

            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow transition">
                Verifikasi & Aktifkan
            </button>
        </form>
    </div>
</body>
</html>