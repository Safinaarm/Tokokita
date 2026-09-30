@extends('layouts.app')

@section('title', 'Dashboard Pembeli - MarketPlace Sederhana')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Profil Pembeli -->
    <div class="bg-gradient-to-r from-pink-600 via-rose-600 to-fuchsia-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-pink-500/15 mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl font-black text-white shadow-inner border border-white/20">
                {{ strtoupper(substr($user->nama_user, 0, 1)) }}
            </div>
            <div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-emerald-400 text-slate-900 shadow-sm">
                    Akun Pembeli Aktif
                </span>
                <h1 class="text-2xl font-black tracking-tight mt-1">{{ $user->nama_user }}</h1>
                <p class="text-xs text-pink-100 flex items-center gap-2 mt-0.5 font-medium">
                    <span><i class="fa-regular fa-envelope"></i> {{ $user->email }}</span>
                    <span>&bull;</span>
                    <span>Bergabung sejak {{ date('d M Y', strtotime($user->tgl_daftar ?? now())) }}</span>
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('landing') }}" class="px-4 py-2.5 bg-white text-pink-700 hover:bg-pink-50 text-xs font-bold rounded-2xl shadow-sm transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-store"></i> Belanja Lagi
            </a>
            <a href="{{ route('pembeli.pesanan.index') }}" class="px-4 py-2.5 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-2xl backdrop-blur-md transition-all flex items-center gap-1.5 border border-white/20">
                <i class="fa-solid fa-bag-shopping"></i> Riwayat Pesanan
            </a>
        </div>
    </div>

    <!-- Rangkuman Pesanan Terbaru -->
    <div class="bg-white rounded-3xl border border-pink-100 p-6 sm:p-8 shadow-sm">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-pink-50">
            <div>
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <span>Pesanan Terbaru Anda</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span>
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Pantau status pengiriman pesanan Anda secara langsung</p>
            </div>
            <a href="{{ route('pembeli.pesanan.index') }}" class="text-xs font-bold text-pink-600 hover:text-pink-700 flex items-center gap-1">
                <span>Lihat Semua Pesanan</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        @if ($recentOrders->isEmpty())
            <div class="text-center py-12">
                <div class="w-14 h-14 bg-pink-50 text-pink-500 rounded-3xl flex items-center justify-center mx-auto mb-3 text-xl border border-pink-100">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Belum Ada Riwayat Pesanan</h4>
                <p class="text-xs text-slate-500 mt-1">Anda belum pernah membuat transaksi pembelian.</p>
                <a href="{{ route('landing') }}" class="inline-block mt-4 px-4 py-2 bg-gradient-to-r from-pink-600 to-rose-600 text-white rounded-xl text-xs font-bold shadow-sm">
                    Mulai Belanja
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-pink-50 text-slate-400 uppercase tracking-wider font-bold">
                            <th class="py-3 px-3">No. Order</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Produk</th>
                            <th class="py-3 px-3">Total Harga</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pink-50">
                        @foreach ($recentOrders as $order)
                            <tr class="hover:bg-pink-50/20 transition-colors">
                                <td class="py-3.5 px-3 font-black text-slate-900">
                                    #{{ $order->id_order }}
                                </td>
                                <td class="py-3.5 px-3 text-slate-500">
                                    {{ date('d M Y, H:i', strtotime($order->tgl_order)) }}
                                </td>
                                <td class="py-3.5 px-3">
                                    @php
                                        $firstItem = $order->details->first();
                                    @endphp
                                    <div class="font-bold text-slate-800 truncate max-w-[200px]">
                                        {{ $firstItem->produk->nama_product ?? 'Produk' }}
                                    </div>
                                    @if ($order->details->count() > 1)
                                        <div class="text-[10px] text-pink-600 font-bold">+{{ $order->details->count() - 1 }} produk lainnya</div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 font-black text-pink-600">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge_class }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <a href="{{ route('pembeli.pesanan.detail', $order->id_order) }}" class="px-3.5 py-1.5 bg-pink-50 text-pink-700 hover:bg-pink-600 hover:text-white rounded-xl font-bold transition-all border border-pink-100 shadow-sm">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection