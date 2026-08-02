<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PegawaiController extends Controller
{
    // Tampilkan Halaman Daftar Pegawai
    public function index()
    {
        $pegawai = User::where('role', 'kasir')->latest()->get();
        return view('pegawai.index', compact('pegawai'));
    }

    // 1. Simpan Data Pegawai & Kirim OTP ke Email
    // app/Http/Controllers/PegawaiController.php

public function store(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:8',
    ]);

    $otp = rand(100000, 999999);

    $user = User::create([
        'name'           => $request->name,
        'email'          => $request->email,
        'password'       => Hash::make($request->password),
        'role'           => 'kasir',
        'otp_code'       => (string)$otp,
        'otp_expires_at' => Carbon::now()->addMinutes(10),
        'is_verified'    => false,
    ]);

    // Kirim email via SMTP (jika gagal, tidak akan bikin crash)
    try {
        Mail::to($user->email)->send(new SendOtpMail($otp));
        $pesan = 'Kode OTP telah dikirimkan ke email pegawai.';
    } catch (\Exception $e) {
        // Tampilkan OTP di alert jika email gagal terkirim
        $pesan = 'Email gagal dikirim. Gunakan Kode OTP berikut untuk pengujian: ' . $otp;
    }

    return redirect()->route('pegawai.otp.page', ['email' => $user->email])
                     ->with('success', $pesan);
}

    // 2. Tampilan Form Input OTP
    public function otpPage(Request $request)
    {
        $email = $request->query('email');
        return view('pegawai.verify_otp', compact('email'));
    }

    // 3. Verifikasi Kode OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|numeric',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->otp_code !== (string)$request->otp) {
            return back()->withErrors(['otp' => 'Kode OTP yang Anda masukkan salah.']);
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp' => 'Kode OTP sudah kadaluarsa. Silakan lakukan pendaftaran ulang.']);
        }

        // Aktifkan akun
        $user->update([
            'is_verified'    => true,
            'otp_code'       => null,
            'otp_expires_at' => null,
        ]);

        return redirect()->route('pegawai.index')->with('success', 'Akun pegawai berhasil diverifikasi & diaktifkan!');
    }

    // 4. Hapus Data Pegawai
    public function destroy($id)
    {
        $pegawai = User::findOrFail($id);
        $pegawai->delete();

        return redirect()->back()->with('success', 'Data pegawai berhasil dihapus.');
    }
}