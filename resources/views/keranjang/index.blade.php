@extends('layouts.app')

@section('title', 'Keranjang Belanja - MarketPlace Sederhana')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex items-center justify-between pb-6 mb-6 border-b border-pink-100">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span>Keranjang Belanja</span>
                <span class="w-2 h-2 rounded-full bg-pink-500"></span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">Periksa kembali barang pilihan Anda sebelum melanjutkan ke pembayaran</p>
        </div>

        @if (!empty($cart))
            <form action="{{ route('keranjang.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua barang dari keranjang belanja?')">
                @csrf
                <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1.5 px-3 py-1.5 rounded-xl hover:bg-rose-50 transition-colors">
                    <i class="fa-regular fa-trash-can"></i> Kosongkan Keranjang
                </button>
            </form>
        @endif
    </div>

    @if (empty($cart))
        <!-- State Keranjang Kosong -->
        <div class="text-center py-16 bg-white rounded-3xl border border-pink-100 shadow-sm p-8 max-w-lg mx-auto">
            <div class="w-20 h-20 bg-pink-50 text-pink-600 rounded-3xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-sm border border-pink-100">
                <i class="fa-solid fa-cart-arrow-down"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900">Keranjang Belanja Masih Kosong</h3>
            <p class="text-sm text-slate-500 mt-2">
                Anda belum menambahkan produk apa pun ke dalam keranjang. Yuk temukan berbagai penawaran menarik!
            </p>
            <div class="mt-6">
                <a href="{{ route('landing') }}" class="px-6 py-3.5 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-bold text-sm rounded-2xl shadow-md shadow-pink-500/25 inline-flex items-center gap-2">
                    <i class="fa-solid fa-store"></i> Mulai Belanja Sekarang
                </a>
            </div>
        </div>
    @else
        <!-- Grid Keranjang & Ringkasan -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Kolom Kiri: Daftar Item Keranjang -->
            <div class="lg:col-span-2 space-y-4">
                @foreach ($cart as $id => $item)
                    <div class="bg-white rounded-3xl border border-pink-100 p-4 sm:p-5 flex flex-col sm:flex-row items-center gap-4 shadow-sm hover:border-pink-200 transition-all">
                        
                        <!-- Gambar Produk -->
                        <div class="w-20 h-20 rounded-2xl overflow-hidden bg-pink-50/40 flex-shrink-0 border border-pink-100">
                            <img src="{{ $item['gambar_product'] }}" alt="{{ $item['nama_product'] }}" class="w-full h-full object-cover">
                        </div>

                        <!-- Info Nama & Harga Satuan -->
                        <div class="flex-1 text-center sm:text-left">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-pink-600 bg-pink-50 px-2 py-0.5 rounded-full border border-pink-100">{{ $item['kategori'] ?? 'Katalog' }}</span>
                            <h4 class="font-bold text-slate-900 text-sm hover:text-pink-600 transition-colors mt-1">
                                <a href="{{ route('produk.detail', $id) }}">{{ $item['nama_product'] }}</a>
                            </h4>
                            <div class="text-xs text-pink-600 font-extrabold mt-1">
                                Rp {{ number_format($item['harga'], 0, ',', '.') }} <span class="text-slate-400 font-normal">/ item</span>
                            </div>
                        </div>

                        <!-- Pengontrol Kuantitas -->
                        <div class="flex items-center gap-3">
                            <div class="flex items-center border border-pink-200 rounded-2xl overflow-hidden bg-pink-50/20">
                                <form action="{{ route('keranjang.update', $id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-pink-100 hover:text-pink-600 transition-colors text-xs font-bold">
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                </form>

                                <span class="w-10 text-center text-xs font-black text-slate-800">
                                    {{ $item['qty'] }}
                                </span>

                                <form action="{{ route('keranjang.update', $id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-pink-100 hover:text-pink-600 transition-colors text-xs font-bold">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Subtotal Item -->
                            <div class="text-right min-w-[100px]">
                                <div class="text-[10px] text-slate-400 font-medium">Subtotal</div>
                                <div class="text-sm font-black text-slate-900">
                                    Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Tombol Hapus Item -->
                            <form action="{{ route('keranjang.remove', $id) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 transition-colors" title="Hapus dari keranjang">
                                    <i class="fa-regular fa-trash-can text-sm"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                @endforeach

                <div class="pt-2">
                    <a href="{{ route('landing') }}" class="text-xs font-bold text-pink-600 hover:text-pink-700 inline-flex items-center gap-1.5 px-3 py-2 rounded-xl hover:bg-pink-50 transition-all">
                        <i class="fa-solid fa-arrow-left"></i> Tambah Produk Lainnya
                    </a>
                </div>
            </div>

            <!-- Kolom Kanan: Ringkasan Belanja -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl border border-pink-100 p-6 shadow-sm sticky top-24">
                    <h3 class="text-base font-black text-slate-900 pb-4 border-b border-pink-50">
                        Ringkasan Belanja
                    </h3>

                    <div class="space-y-3 py-4 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Total Item Dipilih</span>
                            <span class="font-bold text-slate-800">{{ $totalItem }} barang</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Total Harga Barang</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Biaya Pengiriman</span>
                            <span class="font-bold text-emerald-600">Gratis (Promo UJIKOM)</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-pink-50">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Pembayaran</div>
                                <div class="text-xl font-black text-pink-600">
                                    Rp {{ number_format($totalHarga, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        @auth
                            @if (auth()->user()->role === 'pembeli')
                                <a href="{{ route('pembeli.checkout') }}" class="w-full py-3.5 px-4 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-bold rounded-2xl text-sm shadow-md shadow-pink-500/25 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                    <span>Lanjut ke Checkout</span>
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </a>
                            @else
                                <div class="p-3 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-xs font-medium text-center">
                                    Anda login sebagai <strong>Penjual</strong>. Silakan beralih ke akun Pembeli untuk melakukan checkout.
                                </div>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="w-full py-3.5 px-4 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-bold rounded-2xl text-sm shadow-md shadow-pink-500/25 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                <span>Login untuk Checkout</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                            <p class="text-[10px] text-slate-400 text-center mt-2.5">
                                Belum punya akun? <a href="{{ route('register') }}" class="text-pink-600 font-bold underline">Daftar sekarang</a>
                            </p>
                        @endauth
                    </div>
                </div>
            </div>

        </div>
    @endif

</div>
@endsection
