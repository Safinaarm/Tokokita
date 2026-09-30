<?php

namespace App\Http\Controllers;
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekRole
{
    /**
     * Membatasi hak akses halaman berdasarkan role pengguna (penjual / pembeli)
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        }

        $user = Auth::user();

        // Pastikan akun aktif
        if ($user->status_user !== 'aktif') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan. Silakan hubungi admin.');
        }

        // Jika role tidak sesuai, arahkan ke dashboard role yang bersangkutan
        if ($user->role !== $role) {
            if ($user->role === 'penjual') {
                return redirect()->route('penjual.dashboard')->with('error', 'Halaman tersebut hanya untuk Pembeli.');
            }
            return redirect()->route('pembeli.dashboard')->with('error', 'Halaman tersebut hanya untuk Penjual.');
        }

        return $next($request);
    }
}