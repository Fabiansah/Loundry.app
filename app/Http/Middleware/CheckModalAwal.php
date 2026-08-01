<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\BukuKas;
use Symfony\Component\HttpFoundation\Response;

class CheckModalAwal
{
    public function handle(Request $request, Closure $next): Response
    {
        // Admin bebas akses
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }

        // Cek kasir aktif hari ini
        if (auth()->check() && auth()->user()->role === 'kasir') {
            $kasAktif = BukuKas::where('user_id', auth()->id())
                ->where('tanggal', date('Y-m-d'))
                ->where('status', 'buka')
                ->exists();

            if (!$kasAktif) {
                return redirect()->route('modal.create');
            }
        }

        return $next($request);
    }
}