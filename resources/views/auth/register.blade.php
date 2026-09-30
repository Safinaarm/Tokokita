@extends('layouts.app')

@section('title', 'Daftar Akun Baru - MarketPlace Sederhana')

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    
    <div class="bg-white rounded-3xl border border-pink-100 shadow-xl shadow-pink-500/5 p-6 sm:p-8">
        <!-- Header Card -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-pink-50 to-rose-50 text-pink-600 border border-pink-100 flex items-center justify-center mx-auto mb-3 text-xl font-bold shadow-sm">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Formulir Pendaftaran</h2>
            <p class="text-xs text-slate-500 mt-1">Daftarkan akun sebagai Pembeli atau Penjual</p>
        </div>

        <!-- Form Register -->
        <form method="POST" action="{{ route('register.process') }}" class="space-y-4">
            @csrf

            <!-- Pilihan Role (Pembeli vs Penjual) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Daftar Sebagai
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="pembeli" class="peer sr-only" {{ old('role', 'pembeli') === 'pembeli' ? 'checked' : '' }}>
                        <div class="p-3 text-center rounded-2xl border border-pink-100 peer-checked:border-pink-600 peer-checked:bg-pink-50 peer-checked:text-pink-700 hover:bg-pink-50/30 transition-all shadow-sm">
                            <i class="fa-solid fa-cart-shopping block mb-1 text-base"></i>
                            <span class="text-xs font-black">Pembeli</span>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="role" value="penjual" class="peer sr-only" {{ old('role') === 'penjual' ? 'checked' : '' }}>
                        <div class="p-3 text-center rounded-2xl border border-pink-100 peer-checked:border-rose-600 peer-checked:bg-rose-50 peer-checked:text-rose-700 hover:bg-rose-50/30 transition-all shadow-sm">
                            <i class="fa-solid fa-store block mb-1 text-base"></i>
                            <span class="text-xs font-black">Penjual</span>
                        </div>
                    </label>
                </div>
                @error('role')
                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Input Nama Lengkap / Toko -->
            <div>
                <label for="nama_user" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Nama Lengkap / Nama Toko
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-pink-400">
                        <i class="fa-regular fa-user text-sm"></i>
                    </div>
                    <input type="text" id="nama_user" name="nama_user" value="{{ old('nama_user') }}" required autofocus
                           placeholder="Contoh: Budi Santoso / Toko Elektronik"
                           class="w-full pl-10 pr-4 py-2.5 bg-pink-50/20 border @error('nama_user') border-rose-300 ring-1 ring-rose-300 @else border-pink-100 @enderror rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                </div>
                @error('nama_user')
                    <p class="text-xs text-rose-600 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Input Email -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alamat Email
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-pink-400">
                        <i class="fa-regular fa-envelope text-sm"></i>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
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
                    Kata Sandi (Min. 6 Karakter)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-pink-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <input type="password" id="password" name="password" required
                           placeholder="Minimal 6 karakter"
                           class="w-full pl-10 pr-4 py-2.5 bg-pink-50/20 border @error('password') border-rose-300 ring-1 ring-rose-300 @else border-pink-100 @enderror rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                </div>
                @error('password')
                    <p class="text-xs text-rose-600 mt-1 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Input Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Ulangi Kata Sandi
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-pink-400">
                        <i class="fa-solid fa-lock-open text-sm"></i>
                    </div>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           placeholder="Ulangi kata sandi"
                           class="w-full pl-10 pr-4 py-2.5 bg-pink-50/20 border border-pink-100 rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                </div>
            </div>

            <!-- Tombol Submit Daftar -->
            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-bold rounded-2xl text-sm shadow-md shadow-pink-500/25 hover:shadow-lg transition-all flex items-center justify-center gap-2 mt-2">
                <span>Daftar Sekarang</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

    </div>

    <!-- Link ke Login -->
    <div class="text-center mt-6 text-sm text-slate-500">
        Sudah memiliki akun? 
        <a href="{{ route('login') }}" class="font-bold text-pink-600 hover:text-pink-700 underline">
            Masuk di sini
        </a>
    </div>

</div>
@endsection