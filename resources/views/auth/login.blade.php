@extends('layouts.app')

@section('title', 'Masuk Akun - MarketPlace Sederhana')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    
    <div class="bg-white rounded-3xl border border-pink-100 shadow-xl shadow-pink-500/5 p-6 sm:p-8">
        <!-- Header Card -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-pink-50 to-rose-50 text-pink-600 border border-pink-100 flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
            <p class="text-xs text-slate-500 mt-1">Masuk untuk mengelola produk atau berbelanja</p>
        </div>

        <!-- Form Login -->
        <form method="POST" action="{{ route('login.process') }}" class="space-y-4">
            @csrf

            <!-- Input Email -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alamat Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-pink-400">
                        <i class="fa-regular fa-envelope text-sm"></i>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="nama@email.com"
                           class="w-full pl-10 pr-4 py-2.5 bg-pink-50/20 border @error('email') border-rose-300 ring-1 ring-rose-300 @else border-pink-100 @enderror rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                </div>
                @error('email')
                    <p class="text-xs text-rose-600 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Input Password -->
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Kata Sandi
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-pink-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                           placeholder="••••••••"
                           class="w-full pl-10 pr-4 py-2.5 bg-pink-50/20 border @error('password') border-rose-300 ring-1 ring-rose-300 @else border-pink-100 @enderror rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                </div>
                @error('password')
                    <p class="text-xs text-rose-600 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Tombol Submit Login -->
            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-bold rounded-2xl text-sm shadow-md shadow-pink-500/25 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                <span>Masuk ke Akun</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <!-- Informasi Akun UJIKOM Cepat -->
        <div class="mt-6 pt-5 border-t border-pink-50 bg-pink-50/40 -mx-6 -mb-6 p-6 rounded-b-3xl">
            <div class="text-[11px] font-bold uppercase tracking-wider text-pink-800 mb-2.5 flex items-center gap-1.5">
                <i class="fa-solid fa-key text-pink-500"></i> Akun Pengujian UJIKOM (1-Klik Isi):
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" onclick="isiAkun('penjual@ujikom.test', 'password123')"
                        class="p-2.5 bg-white border border-pink-100 hover:border-pink-400 hover:bg-pink-50/50 rounded-xl text-left transition-all group shadow-sm">
                    <div class="font-extrabold text-slate-800 flex items-center justify-between">
                        <span>Penjual</span>
                        <i class="fa-solid fa-arrow-turn-down text-slate-300 group-hover:text-pink-500 text-[10px]"></i>
                    </div>
                    <div class="text-[10px] text-slate-500 truncate mt-0.5">penjual@ujikom.test</div>
                </button>

                <button type="button" onclick="isiAkun('pembeli@ujikom.test', 'password123')"
                        class="p-2.5 bg-white border border-pink-100 hover:border-pink-400 hover:bg-pink-50/50 rounded-xl text-left transition-all group shadow-sm">
                    <div class="font-extrabold text-slate-800 flex items-center justify-between">
                        <span>Pembeli</span>
                        <i class="fa-solid fa-arrow-turn-down text-slate-300 group-hover:text-pink-500 text-[10px]"></i>
                    </div>
                    <div class="text-[10px] text-slate-500 truncate mt-0.5">pembeli@ujikom.test</div>
                </button>
            </div>
            <p class="text-[10px] text-pink-600/70 text-center mt-2.5">Password default: <code class="bg-pink-100 px-1.5 py-0.5 rounded text-pink-700 font-mono font-bold">password123</code></p>
        </div>

    </div>

    <!-- Link ke Pendaftaran -->
    <div class="text-center mt-6 text-sm text-slate-500">
        Belum memiliki akun? 
        <a href="{{ route('register') }}" class="font-bold text-pink-600 hover:text-pink-700 underline">
            Daftar sekarang
        </a>
    </div>

</div>

<script>
    function isiAkun(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }
</script>
@endsection