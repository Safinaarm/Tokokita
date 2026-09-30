@extends('layouts.app')

@section('title', $produk->nama_product . ' - Detail Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Breadcrumb Navigasi -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('landing') }}" class="hover:text-pink-600">Beranda</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-pink-300"></i>
        <a href="{{ route('landing', ['kategori' => $produk->kategori]) }}" class="hover:text-pink-600">{{ $produk->kategori }}</a>
        <i class="fa-solid fa-chevron-right text-[10px] text-pink-300"></i>
        <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $produk->nama_product }}</span>
    </nav>

    <!-- Card Detail Produk -->
    <div class="bg-white rounded-3xl border border-pink-100 shadow-sm overflow-hidden mb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 sm:p-10">
            
            <!-- Kolom Kiri: Foto Produk -->
            <div class="space-y-4">
                <div class="aspect-square rounded-3xl overflow-hidden bg-pink-50/40 border border-pink-100 shadow-inner">
                    <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama_product }}" 
                         class="w-full h-full object-cover"
                         onerror="this.src='https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&auto=format&fit=crop&q=60'">
                </div>
            </div>

            <!-- Kolom Kanan: Informasi & Form Aksi -->
            <div class="flex flex-col justify-between">
                <div>
                    <!-- Badge Kategori & Status -->
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 bg-pink-50 text-pink-700 border border-pink-100 text-xs font-black rounded-xl">
                            {{ $produk->kategori }}
                        </span>

                        @if ($produk->status_product === 'tersedia' && $produk->stok > 0)
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-xl flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Tersedia (Stok: {{ $produk->stok }})
                            </span>
                        @else
                            <span class="px-3 py-1 bg-rose-50 text-rose-700 border border-rose-200 text-xs font-bold rounded-xl">
                                Stok Habis
                            </span>
                        @endif
                    </div>

                    <!-- Nama Produk -->
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight leading-tight">
                        {{ $produk->nama_product }}
                    </h1>

                    <!-- Info Penjual / Toko -->
                    <div class="flex items-center gap-2.5 mt-3 pb-4 border-b border-pink-50">
                        <div class="w-8 h-8 rounded-full bg-pink-50 text-pink-600 flex items-center justify-center font-bold text-xs border border-pink-100">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <span class="text-[11px] text-slate-400">Dijual oleh</span>
                            <p class="text-xs font-bold text-slate-800">{{ $produk->penjual->nama_user ?? 'Penjual' }}</p>
                        </div>
                    </div>

                    <!-- Harga Produk -->
                    <div class="my-5">
                        <div class="text-xs text-slate-400 font-medium">Harga Satuan</div>
                        <div class="text-3xl font-black text-pink-600">
                            {{ $produk->harga_rupiah }}
                        </div>
                    </div>

                    <!-- Deskripsi Produk -->
                    <div class="mb-6">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-pink-700/80 mb-2">Deskripsi Produk</h3>
                        <div class="text-sm text-slate-600 leading-relaxed whitespace-pre-line bg-pink-50/30 p-4 rounded-2xl border border-pink-100">
                            {{ $produk->deskripsi ?? 'Tidak ada deskripsi detail untuk produk ini.' }}
                        </div>
                    </div>
                </div>

                <!-- Form Tambah ke Keranjang -->
                <div class="pt-6 border-t border-pink-50">
                    @if ($produk->status_product === 'tersedia' && $produk->stok > 0)
                        <form action="{{ route('keranjang.add', $produk->id_product) }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <div class="flex items-center gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 mb-1">Jumlah (Qty)</label>
                                    <div class="flex items-center border border-pink-200 rounded-2xl overflow-hidden bg-pink-50/20 w-32">
                                        <button type="button" onclick="ubahQty(-1)" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-pink-100 hover:text-pink-600 transition-colors font-bold text-sm">
                                            <i class="fa-solid fa-minus text-xs"></i>
                                        </button>
                                        <input type="number" id="qtyInput" name="qty" value="1" min="1" max="{{ $produk->stok }}"
                                               class="w-12 text-center bg-transparent text-sm font-black text-slate-800 focus:outline-none">
                                        <button type="button" onclick="ubahQty(1)" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-pink-100 hover:text-pink-600 transition-colors font-bold text-sm">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-transparent mb-1">Aksi</label>
                                    <button type="submit" class="w-full h-10 px-6 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-bold rounded-2xl text-sm shadow-md shadow-pink-500/25 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-cart-plus"></i>
                                        <span>Tambah ke Keranjang</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-semibold flex items-center gap-2">
                            <i class="fa-solid fa-circle-xmark"></i>
                            <span>Mohon maaf, persediaan produk ini sedang habis saat ini.</span>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>

    <!-- Produk Terkait -->
    @if ($produkTerkait->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-xl font-black text-slate-900 mb-6 flex items-center gap-2">
                <span>Produk Terkait Lainnya</span>
                <span class="w-2 h-2 rounded-full bg-pink-500"></span>
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach ($produkTerkait as $terkait)
                    <a href="{{ route('produk.detail', $terkait->id_product) }}" class="group bg-white rounded-3xl border border-pink-100 p-3.5 hover:shadow-lg hover:shadow-pink-500/10 hover:border-pink-300 transition-all">
                        <div class="aspect-square rounded-2xl overflow-hidden bg-pink-50/40 mb-3">
                            <img src="{{ $terkait->gambar_url }}" alt="{{ $terkait->nama_product }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <h4 class="text-xs font-bold text-slate-900 group-hover:text-pink-600 truncate">{{ $terkait->nama_product }}</h4>
                        <div class="text-xs font-black text-pink-600 mt-1">{{ $terkait->harga_rupiah }}</div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>

<script>
    function ubahQty(delta) {
        const input = document.getElementById('qtyInput');
        let current = parseInt(input.value) || 1;
        let max = parseInt(input.max) || 999;
        let min = parseInt(input.min) || 1;
        let nextVal = current + delta;
        if (nextVal >= min && nextVal <= max) {
            input.value = nextVal;
        }
    }
</script>
@endsection
