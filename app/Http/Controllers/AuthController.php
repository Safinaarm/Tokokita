<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Controller Autentikasi Pengguna
 * Menangani alur pendaftaran, login multi-role (penjual & pembeli), dan logout
 */
class AuthController extends Controller
{
    /**
     * Menampilkan formulir pendaftaran pengguna baru
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran akun baru
     * Dapat mendaftar sebagai Pembeli maupun Penjual
     */
    public function register(Request $request)
    {
        // Validasi input data dari formulir pendaftaran
        $data = $request->validate([
            'nama_user' => 'required|string|max:100',
            'email'     => 'required|email|max:100|unique:users,email',
            'password'  => 'required|string|min:6|confirmed',
            'role'      => 'required|in:pembeli,penjual',
        ], [
            'nama_user.required' => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required'      => 'Pilih peran akun (Pembeli atau Penjual).',
            'role.in'            => 'Peran akun tidak valid.',
        ]);

        // Simpan data akun ke tabel users
        User::create([
            'nama_user'   => $data['nama_user'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']), // Mengamankan password dengan enkripsi Bcrypt
            'role'        => $data['role'],
            'tgl_daftar'  => now(),
            'status_user' => 'aktif',
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan masuk menggunakan akun Anda.');
    }

    /**
     * Menampilkan formulir login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Memproses autentikasi login pengguna
     * Memeriksa kredensial email & password, serta status keaktifan user
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Tambahkan syarat user harus berstatus aktif
        $credentials['status_user'] = 'aktif';

        if (Auth::attempt($credentials)) {
            // Regenerasi session ID untuk mencegah serangan session fixation
            $request->session()->regenerate();

            // Arahkan ke dashboard yang sesuai berdasarkan role pengguna
            $user = Auth::user();
            if ($user->role === 'penjual') {
                return redirect()->route('penjual.dashboard')->with('success', 'Selamat datang kembali, ' . $user->nama_user . '!');
            }

            return redirect()->route('pembeli.dashboard')->with('success', 'Selamat datang, ' . $user->nama_user . '!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah, atau akun Anda sedang tidak aktif.',
        ])->onlyInput('email');
    }

    /**
     * Memproses logout pengguna dan menghapus sesi aktif
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('info', 'Anda telah berhasil keluar.');
    }
}