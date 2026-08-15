<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PegawaiController extends Controller
{
    // 1. Tampilkan Halaman Kelola Pegawai
    public function index()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses khusus Admin.');
        }

        $pegawais = User::where('role', 'kasir')->latest()->get();
        return view('pegawai.index', compact('pegawais'));
    }

    // 2. Admin Mendaftarkan Kasir & Otomatis Kirim Link via WA Gateway
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'no_hp' => 'required|string|max:20',
        ]);

        // Format nomor HP standar (08xxx -> 628xxx)
        $cleanPhone = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $request->no_hp));

        // Generate Token Unik berlaku 24 jam
        $token = Str::random(40);

        $user = User::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'no_hp'            => $cleanPhone,
            'role'             => 'kasir',
            'password'         => null,
            'invitation_token' => $token,
            'token_expires_at' => Carbon::now()->addHours(24),
            'status_akun'      => 'pending',
        ]);

        // Siapkan tautan aktivasi
        $activationUrl = route('pegawai.aktivasi.form', $token);
        
$pesan = "Hai *{$user->name}*,\n\n"
       . "Akun Anda telah didaftarkan sebagai *Kasir Laundry* pada sistem kami."
       . "Silakan akses tautan berikut untuk membuat kata sandi dan menyelesaikan proses aktivasi akun Anda:\n\n"
       . "{$activationUrl}\n\n"
       . "Catatan: Tautan ini bersifat rahasia dan hanya berlaku selama 24 jam ke depan.\n\n"
       . "Terima kasih.";

        $waTerkirim = false;

        // KIRIM OTOMATIS VIA WHATSAPP GATEWAY (Fonnte API)
        if (env('FONNTE_TOKEN') && env('FONNTE_TOKEN') !== 'isi_token_fonnte_anda_disini') {
            try {
                $response = Http::withHeaders([
                    'Authorization' => env('FONNTE_TOKEN'),
                ])->post('https://api.fonnte.com/send', [
                    'target'  => $cleanPhone,
                    'message' => $pesan,
                ]);

                if ($response->successful() && isset($response->json()['status']) && $response->json()['status'] == true) {
                    $waTerkirim = true;
                } else {
                    Log::error('Fonnte Error: ' . json_encode($response->json()));
                }
            } catch (\Exception $e) {
                Log::error('Gagal kirim pesan WhatsApp otomatis: ' . $e->getMessage());
            }
        }

        // Jika Fonnte berhasil terkirim
        if ($waTerkirim) {
            return redirect()->route('pegawai.index')
                ->with('sukses', "Undangan aktivasi berhasil dikirim otomatis ke WhatsApp {$user->name}!");
        }

        // Fallback: Jika Fonnte belum aktif/gagal, sediakan link langsung agar bisa dicoba
        return redirect()->route('pegawai.index')
            ->with('sukses', "Undangan untuk {$user->name} berhasil dibuat!")
            ->with('link_manual', $activationUrl)
            ->with('kasir_name', $user->name);
    }
    // 3. Form Aktivasi Password (Diakses Karyawan dari HP)
    public function showAktivasiForm($token)
    {
        $user = User::where('invitation_token', $token)
            ->where('status_akun', 'pending')
            ->first();

        if (!$user) {
            return view('pages.auth.aktivasi_expired', [
                'pesan' => 'Tautan aktivasi tidak valid atau akun sudah aktif.'
            ]);
        }

        if (Carbon::now()->greaterThan($user->token_expires_at)) {
            return view('pages.auth.aktivasi_expired', [
                'pesan' => 'Tautan aktivasi sudah kadaluarsa (lebih dari 24 jam). Hubungi Admin untuk mengirim ulang.'
            ]);
        }

        return view('pages.auth.aktivasi', compact('user', 'token'));
    }

    // 4. Proses Simpan Password & Aktifkan Akun
    public function prosesAktivasi(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal harus 8 karakter.',
        ]);

        $user = User::where('invitation_token', $token)
            ->where('status_akun', 'pending')
            ->firstOrFail();

        if (Carbon::now()->greaterThan($user->token_expires_at)) {
            return redirect()->route('login')->with('error', 'Tautan telah kadaluarsa.');
        }

        $user->update([
            'password'         => Hash::make($request->password),
            'invitation_token' => null,
            'token_expires_at' => null,
            'status_akun'      => 'aktif',
        ]);

        return redirect()->route('login')->with('sukses', 'Akun Kasir berhasil diaktifkan! Silakan login.');
    }

    // 5. Hapus Pegawai
    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        if (auth()->id() == $id) {
            return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun Admin Anda sendiri!');
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('sukses', 'Akun pegawai berhasil dihapus.');
    }
}