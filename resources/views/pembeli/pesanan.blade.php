@extends('layouts.app')

@section('title', 'Daftar Pesanan Saya - MarketPlace Sederhana')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Daftar Pesanan Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Lacak status pesanan, pembayaran, dan riwayat belanja Anda</p>
        </div>
        <div>
            <a href="{{ route('landing') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all inline-flex items-center gap-2">
                <i class="fa-solid fa-cart-plus"></i> Belanja Lagi
            </a>
        </div>
    </div>

    @if ($orders->isEmpty())
        <div class="text-center py-16 bg-white rounded-3xl border border-slate-200 shadow-sm p-8 max-w-lg mx-auto">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-4 text-2xl">
                <i class="fa-solid fa-box-archive"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Belum Ada Riwayat Pesanan</h3>
            <p class="text-sm text-slate-500 mt-1">
                Anda belum melakukan transaksi belanja di TokoKita. Temukan produk favorit Anda sekarang!
            </p>
            <div class="mt-6">
                <a href="{{ route('landing') }}" class="px-6 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl shadow-sm">
                    Mulai Belanja
                </a>
            </div>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-slate-300 transition-all">
                    
                    <!-- Header Kartu Pesanan -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-100 text-xs">
                        <div class="flex items-center gap-3">
                            <span class="font-black text-slate-900 text-sm">#ORDER-{{ $order->id_order }}</span>
                            <span class="text-slate-400">&bull;</span>
                            <span class="text-slate-500"><i class="fa-regular fa-calendar mr-1"></i> {{ date('d M Y, H:i', strtotime($order->tgl_order)) }}</span>
                        </div>

                        <div>
                            <!-- Badge Status Order -->
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $order->status_badge_class }}">
                                {{ $order->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- List Ringkas Produk -->
                    <div class="py-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                        <div class="md:col-span-2 space-y-3">
                            @foreach ($order->details as $item)
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->produk->gambar_url ?? '' }}" alt="" class="w-12 h-12 rounded-xl object-cover bg-slate-100 flex-shrink-0 border border-slate-100">
                                    <div class="min-w-0 flex-1">
                                        <h5 class="text-xs font-bold text-slate-900 truncate">
                                            {{ $item->produk->nama_product ?? 'Produk Dihapus' }}
                                        </h5>
                                        <p class="text-[11px] text-slate-500">
                                            {{ $item->qty }} item &times; Rp {{ number_format($item->harga, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    <div class="text-xs font-bold text-slate-800">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Kolom Total & Tombol Aksi -->
                        <div class="md:border-l md:border-slate-100 md:pl-6 flex flex-col justify-between h-full pt-4 md:pt-0 border-t md:border-t-0 border-slate-100">
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Total Tagihan</div>
                                <div class="text-lg font-black text-blue-600">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <a href="{{ route('pembeli.pesanan.detail', $order->id_order) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                                    <i class="fa-regular fa-file-lines"></i>
                                    <span>Rincian & Pembayaran</span>
                                </a>

                                @if ($order->status_order === 'dikirim')
                                    <form action="{{ route('pembeli.pesanan.selesai', $order->id_order) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa barang pesanan telah Anda terima dengan baik?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                                            <i class="fa-solid fa-check-double"></i>
                                            <span>Terima Pesanan</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach

            <!-- Pagination -->
            <div class="pt-4 flex justify-center">
                {{ $orders->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
