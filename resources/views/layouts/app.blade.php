<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'MarketPlace Sederhana') - TokoKita</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            200: '#fecdd3',
                            300: '#fda4af',
                            400: '#fb7185',
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Font Awesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Alpine.js CDN for interactive modals & dropdowns -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom scrollbar subtle pink */
        ::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        ::-webkit-scrollbar-thumb {
            background: #fda4af;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-track {
            background: #fff1f2;
        }
    </style>
    @yield('styles')
</head>
<body class="bg-[#faf5f6] text-slate-800 min-h-screen flex flex-col antialiased">

    <!-- Header & Navigasi Utama -->
    <header class="bg-white/90 backdrop-blur-md border-b border-pink-100 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-pink-600 via-rose-500 to-pink-500 flex items-center justify-center text-white shadow-md shadow-pink-500/25 group-hover:scale-105 transition-transform">
                            <i class="fa-solid fa-bag-shopping text-lg"></i>
                        </div>
                        <div>
                            <span class="text-xl font-black tracking-tight text-slate-900 group-hover:text-pink-600 transition-colors">Toko<span class="text-pink-600">Kita</span></span>
                            <span class="hidden sm:inline-block text-[10px] font-bold tracking-wider text-pink-700 uppercase ml-1.5 px-2 py-0.5 bg-pink-50 border border-pink-200/60 rounded-full">Marketplace</span>
                        </div>
                    </a>

                    <!-- Navigasi Publik -->
                    <nav class="hidden md:flex items-center gap-1 ml-6">
                        <a href="{{ route('landing') }}" class="px-3.5 py-2 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('landing') ? 'text-pink-600 bg-pink-50 border border-pink-100' : 'text-slate-600 hover:text-pink-600 hover:bg-pink-50/50' }}">
                            <i class="fa-solid fa-store mr-1.5 text-xs text-pink-500"></i> Katalog Produk
                        </a>
                    </nav>
                </div>

                <!-- Bagian Kanan Header -->
                <div class="flex items-center gap-3">
                    <!-- Icon Keranjang Belanja -->
                    @php
                        $cartCount = count(session('cart', []));
                    @endphp
                    <a href="{{ route('keranjang.index') }}" class="relative p-2.5 rounded-xl text-slate-600 hover:text-pink-600 hover:bg-pink-50 border border-transparent hover:border-pink-100 transition-all" title="Keranjang Belanja">
                        <i class="fa-solid fa-cart-shopping text-lg"></i>
                        @if ($cartCount > 0)
                            <span class="absolute -top-1 -right-1 bg-gradient-to-r from-pink-600 to-rose-600 text-white text-[11px] font-extrabold w-5 h-5 rounded-full flex items-center justify-center shadow-md shadow-pink-500/30 ring-2 ring-white">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Menu Pengguna Berdasarkan Status Login -->
                    @auth
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" type="button" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-full border border-pink-100 hover:border-pink-300 hover:bg-pink-50/50 transition-all focus:outline-none">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-pink-500 to-rose-500 text-white font-black flex items-center justify-center text-xs shadow-sm">
                                    {{ strtoupper(substr(auth()->user()->nama_user, 0, 1)) }}
                                </div>
                                <div class="text-left hidden sm:block">
                                    <div class="text-xs font-bold text-slate-800 leading-tight max-w-[120px] truncate">{{ auth()->user()->nama_user }}</div>
                                    <div class="text-[10px] font-semibold text-pink-600 capitalize">{{ auth()->user()->role }}</div>
                                </div>
                                <i class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform" :class="{'rotate-180': open}"></i>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-cloak
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-pink-100 py-2 z-50">
                                
                                <div class="px-4 py-2.5 border-b border-pink-50">
                                    <p class="text-xs text-slate-400 font-medium">Masuk sebagai</p>
                                    <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->email }}</p>
                                    <span class="inline-block mt-1 text-[10px] uppercase font-extrabold px-2.5 py-0.5 rounded-full {{ auth()->user()->role === 'penjual' ? 'bg-purple-100 text-purple-700' : 'bg-pink-100 text-pink-700' }}">
                                        {{ auth()->user()->role }}
                                    </span>
                                </div>

                                @if (auth()->user()->role === 'penjual')
                                    <!-- Menu Khusus Penjual -->
                                    <a href="{{ route('penjual.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-pink-50 hover:text-pink-600 font-medium">
                                        <i class="fa-solid fa-chart-pie w-4 text-pink-400"></i> Dashboard Penjual
                                    </a>
                                    <a href="{{ route('penjual.produk.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-pink-50 hover:text-pink-600 font-medium">
                                        <i class="fa-solid fa-boxes-stacked w-4 text-pink-400"></i> Kelola Produk
                                    </a>
                                    <a href="{{ route('penjual.order.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-pink-50 hover:text-pink-600 font-medium">
                                        <i class="fa-solid fa-clipboard-list w-4 text-pink-400"></i> Manajemen Pesanan
                                    </a>
                                @else
                                    <!-- Menu Khusus Pembeli -->
                                    <a href="{{ route('pembeli.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-pink-50 hover:text-pink-600 font-medium">
                                        <i class="fa-solid fa-user-circle w-4 text-pink-400"></i> Dashboard Saya
                                    </a>
                                    <a href="{{ route('pembeli.pesanan.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-pink-50 hover:text-pink-600 font-medium">
                                        <i class="fa-solid fa-bag-shopping w-4 text-pink-400"></i> Pesanan Saya
                                    </a>
                                    <a href="{{ route('keranjang.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-slate-700 hover:bg-pink-50 hover:text-pink-600 font-medium">
                                        <i class="fa-solid fa-cart-shopping w-4 text-pink-400"></i> Keranjang Belanja
                                    </a>
                                @endif

                                <div class="border-t border-pink-50 my-1"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 text-left font-semibold">
                                        <i class="fa-solid fa-right-from-bracket w-4 text-rose-400"></i> Keluar (Logout)
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Tombol Masuk & Daftar untuk Tamu -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-bold text-slate-700 hover:text-pink-600 hover:bg-pink-50 rounded-xl transition-all">
                                Masuk
                            </a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-bold text-white bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 rounded-xl shadow-md shadow-pink-500/25 hover:shadow-lg transition-all">
                                Daftar Akun
                            </a>
                        </div>
                    @endauth

                </div>
            </div>
        </div>
    </header>

    <!-- Flash Alert Notifikasi Global -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if (session('success'))
            <div class="flex items-center justify-between p-4 mb-3 text-sm text-emerald-800 rounded-2xl bg-emerald-50 border border-emerald-200 shadow-sm" role="alert">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <div>
                        <span class="font-bold">Berhasil!</span> {{ session('success') }}
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center justify-between p-4 mb-3 text-sm text-rose-800 rounded-2xl bg-rose-50 border border-rose-200 shadow-sm" role="alert">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                    <div>
                        <span class="font-bold">Perhatian:</span> {{ session('error') }}
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        @if (session('info'))
            <div class="flex items-center justify-between p-4 mb-3 text-sm text-pink-900 rounded-2xl bg-pink-50 border border-pink-200 shadow-sm" role="alert">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-info text-pink-600 text-lg"></i>
                    <div>
                        <span class="font-bold">Informasi:</span> {{ session('info') }}
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-pink-500 hover:text-pink-800 p-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Konten Halaman Dinamis -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Aplikasi Sederhana -->
    <footer class="bg-white border-t border-pink-100 mt-16 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-slate-700">
                    <div class="w-7 h-7 rounded-xl bg-gradient-to-tr from-pink-600 to-rose-500 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <span class="font-black text-slate-900">Toko<span class="text-pink-600">Kita</span></span>
                    <span class="text-slate-400 text-sm">- Platform MarketPlace Sederhana</span>
                </div>
                <div class="text-xs text-slate-500">
                    &copy; {{ date('Y') }} Uji Kompetensi Keahlian (UJIKOM) - Junior Web Programmer.
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
