<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

Route::middleware('guest')->group(function () {
    // --- FITUR REGISTER ---
    // Route::get('register', function () {
    //     return view('pages.auth.register');
    // })->name('register');

    // Route::post('register', function (Request $request) {
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'role' => $request->role ?? 'kasir',
    //         'password' => Hash::make($request->password),
    //     ]);

    //     Auth::login($user);
    //     return redirect('/transaksi');
    // });

    // --- FITUR LOGIN ---
    Route::get('login', function () {
        return view('pages.auth.login');
    })->name('login');

    Route::post('login', function (Request $request) {
        $credentials = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Mencoba mencocokkan nama pengguna & password ke database
        if (Auth::attempt($request->only('name', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Jika cocok, langsung lempar ke halaman dashboard atau halaman yang dimaksud
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Jika salah, kembalikan ke halaman login dengan pesan error
        throw ValidationException::withMessages([
            'name' => __('auth.failed'),
        ]);
    });
});

Route::middleware('auth')->group(function () {
    // --- FITUR LOGOUT ---
    Route::post('logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});