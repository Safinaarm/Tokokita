@extends('layouts.app')

@section('title', 'Katalog & Beranda - MarketPlace Sederhana')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Hero Banner Pink Aesthetic Modern -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-pink-600 via-rose-600 to-fuchsia-700 text-white shadow-xl shadow-pink-500/15 mb-10">
        <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="relative px-6 py-12 md:py-16 md:px-12 max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md text-white border border-white/25 mb-4 shadow-sm">
                <i class="fa-solid fa-sparkles text-amber-300"></i> Platform Belanja Online Terpercaya
            </span>
            <h1 class="text-3xl md:text-5xl font-black tracking-tight leading-tight">
                Temukan Produk Pilihan dengan Transaksi Mudah & Nyaman.
            </h1>
            <p class="mt-4 text-pink-100 text-base md:text-lg leading-relaxed">
                Jelajahi berbagai produk terbaik dari penjual terpercaya kami. Dapatkan penawaran menarik dan kemudahan berbelanja online.
            </p>
            <div class="mt-7 flex flex-wrap items-center gap-3">
                <a href="#katalog" class="px-6 py-3.5 rounded-2xl bg-white text-pink-700 font-extrabold text-sm shadow-lg hover:bg-pink-50 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-bag-shopping"></i> Belanja Sekarang
                </a>
                @guest
                    <a href="{{ route('register') }}" class="px-6 py-3.5 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-md border border-white/30 text-white font-bold text-sm transition-all flex items-center gap-2">
                        <i class="fa-solid fa-store"></i> Mulai Berjualan
                    </a>
                @endguest
            </div>
        </div>
    </div>

    <!-- Section Filter Kategori & Pencarian -->
    <div id="katalog" class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-pink-100">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Katalog Produk Pilihan</span>
                    <span class="w-2 h-2 rounded-full bg-pink-500"></span>
                </h2>
                <p class="text-sm text-slate-500 mt-1">Temukan berbagai koleksi produk berkualitas sesuai kebutuhan Anda</p>
            </div>

            <!-- Form Pencarian -->
            <form action="{{ route('landing') }}" method="GET" class="flex items-center gap-2 max-w-md w-full">
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-pink-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama produk atau deskripsi..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-pink-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all shadow-sm">
                </div>
                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white text-sm font-bold rounded-2xl shadow-md shadow-pink-500/20 transition-all">
                    Cari
                </button>
            </form>
        </div>

        <!-- Filter Pill Kategori -->
        <div class="flex items-center gap-2 overflow-x-auto py-4 scrollbar-none">
            <a href="{{ route('landing', array_merge(request()->except('kategori', 'page'), ['kategori' => 'Semua'])) }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ (!request('kategori') || request('kategori') === 'Semua') ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-md shadow-pink-500/25' : 'bg-white text-slate-600 border border-pink-100 hover:bg-pink-50/50 hover:text-pink-600' }}">
                <i class="fa-solid fa-grid-2 mr-1"></i> Semua Kategori
            </a>
            @foreach ($kategoriList as $kat)
                <a href="{{ route('landing', array_merge(request()->except('kategori', 'page'), ['kategori' => $kat])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ request('kategori') === $kat ? 'bg-gradient-to-r from-pink-600 to-rose-600 text-white shadow-md shadow-pink-500/25' : 'bg-white text-slate-600 border border-pink-100 hover:bg-pink-50/50 hover:text-pink-600' }}">
                    {{ $kat }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Grid Produk -->
    @if ($produk->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl border border-pink-100 shadow-sm p-8">
            <div class="w-16 h-16 bg-pink-50 text-pink-500 rounded-3xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-box-open text-2xl"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Tidak ada produk ditemukan</h3>
            <p class="text-sm text-slate-500 mt-1 max-w-sm mx-auto">
                Silakan coba ubah kata kunci pencarian atau pilih kategori produk yang lain.
            </p>
            <a href="{{ route('landing') }}" class="inline-block mt-4 px-4 py-2 bg-pink-50 hover:bg-pink-100 text-pink-700 text-xs font-bold rounded-xl border border-pink-200">
                Reset Filter
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($produk as $item)
                <div class="group bg-white rounded-3xl border border-pink-100 shadow-sm hover:shadow-xl hover:shadow-pink-500/10 hover:border-pink-300 transition-all duration-300 flex flex-col overflow-hidden">
                    
                    <!-- Gambar Produk -->
                    <div class="relative aspect-square overflow-hidden bg-pink-50/40">
                        <img src="{{ $item->gambar_url }}" alt="{{ $item->nama_product }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             onerror="this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&auto=format&fit=crop&q=60'">
                        
                        <!-- Badge Kategori -->
                        <div class="absolute top-3 left-3">
                            <span class="px-3 py-1 bg-white/95 backdrop-blur-md text-[11px] font-extrabold text-pink-700 border border-pink-100 rounded-xl shadow-sm">
                                {{ $item->kategori }}
                            </span>
                        </div>

                        <!-- Status Ketersediaan -->
                        <div class="absolute top-3 right-3">
                            @if ($item->status_product === 'tersedia' && $item->stok > 0)
                                <span class="px-2.5 py-1 bg-emerald-500 text-white text-[11px] font-bold rounded-xl shadow-sm">
                                    Tersedia ({{ $item->stok }})
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-rose-500 text-white text-[11px] font-bold rounded-xl shadow-sm">
                                    Stok Habis
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Informasi Produk -->
                    <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Info Penjual -->
                            <div class="flex items-center gap-1.5 text-xs text-slate-400 mb-1.5 font-medium">
                                <i class="fa-solid fa-store text-[10px] text-pink-400"></i>
                                <span class="truncate">{{ $item->penjual->nama_user ?? 'Penjual' }}</span>
                            </div>

                            <!-- Nama Produk -->
                            <h3 class="font-bold text-slate-900 group-hover:text-pink-600 transition-colors line-clamp-2 text-sm leading-snug">
                                <a href="{{ route('produk.detail', $item->id_product) }}">
                                    {{ $item->nama_product }}
                                </a>
                            </h3>

                            <!-- Deskripsi Singkat -->
                            <p class="text-xs text-slate-500 line-clamp-2 mt-2 leading-relaxed">
                                {{ $item->deskripsi ?? 'Tidak ada deskripsi produk.' }}
                            </p>
                        </div>

                        <!-- Harga & Tombol Aksi -->
                        <div class="pt-4 mt-4 border-t border-pink-50 flex items-center justify-between gap-2">
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Harga</div>
                                <div class="text-base font-black text-pink-600">
                                    {{ $item->harga_rupiah }}
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('produk.detail', $item->id_product) }}" 
                                   class="p-2 text-slate-600 hover:text-pink-600 hover:bg-pink-50 rounded-xl transition-colors" title="Lihat Detail">
                                    <i class="fa-regular fa-eye"></i>
                                </a>

                                @if ($item->status_product === 'tersedia' && $item->stok > 0)
                                    <form action="{{ route('keranjang.add', $item->id_product) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-2 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-pink-500/20 hover:shadow-lg transition-all flex items-center gap-1.5">
                                            <i class="fa-solid fa-cart-plus"></i>
                                            <span class="hidden sm:inline">Beli</span>
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="px-3 py-2 bg-slate-100 text-slate-400 rounded-xl text-xs font-medium cursor-not-allowed">
                                        Habis
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8 flex justify-center">
            {{ $produk->links() }}
        </div>
    @endif

</div>
@endsection