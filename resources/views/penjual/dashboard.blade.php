@extends('layouts.app')

@section('title', 'Dashboard Penjual - MarketPlace Sederhana')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Selamat Datang Penjual -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/30 text-blue-200 border border-blue-400/30">
                Panel Penjual / Admin Toko
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight mt-1">
                Halo, {{ auth()->user()->nama_user }} 👋
            </h1>
            <p class="text-xs text-slate-300 mt-1">
                Kelola inventaris produk dan pantau pesanan pelanggan dari satu dasbor terpadu.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('penjual.produk.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Tambah Produk
            </a>
            <a href="{{ route('penjual.order.index') }}" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-xl backdrop-blur-md transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-clipboard-list"></i> Kelola Pesanan
            </a>
        </div>
    </div>

    <!-- 4 Kartu Statistik Metrik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        <!-- Total Produk -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Produk</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1">{{ $totalProduk }}</h3>
                <a href="{{ route('penjual.produk.index') }}" class="text-[11px] font-bold text-blue-600 hover:underline mt-1 inline-block">
                    Lihat katalog &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
        </div>

        <!-- Butuh Konfirmasi -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Perlu Konfirmasi</p>
                <h3 class="text-2xl font-black text-amber-600 mt-1">{{ $orderPerluKonfirmasi }}</h3>
                <a href="{{ route('penjual.order.index', ['status' => 'menunggu_konfirmasi']) }}" class="text-[11px] font-bold text-amber-600 hover:underline mt-1 inline-block">
                    Cek bukti bayar &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
        </div>

        <!-- Sedang Diproses -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sedang Diproses</p>
                <h3 class="text-2xl font-black text-indigo-600 mt-1">{{ $orderDiproses }}</h3>
                <a href="{{ route('penjual.order.index', ['status' => 'diproses']) }}" class="text-[11px] font-bold text-indigo-600 hover:underline mt-1 inline-block">
                    Siap kirim &rarr;
                </a>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-box"></i>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendapatan Selesai</p>
                <h3 class="text-lg font-black text-emerald-600 mt-1">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </h3>
                <span class="text-[11px] text-slate-400 mt-1 inline-block">Dari pesanan tuntas</span>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
        </div>

    </div>

    <!-- Tabel Pesanan Masuk Terbaru -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Pesanan Masuk Terbaru</h2>
                <p class="text-xs text-slate-500 mt-0.5">Segera verifikasi bukti pembayaran dan kirim barang ke pembeli</p>
            </div>
            <a href="{{ route('penjual.order.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                <span>Lihat Semua Pesanan</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        @if ($recentOrders->isEmpty())
            <div class="text-center py-12">
                <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-3 text-xl">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">Belum Ada Pesanan Masuk</h4>
                <p class="text-xs text-slate-500 mt-1">Pesanan dari pembeli akan otomatis muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider">
                            <th class="py-3 px-3">No. Order</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Pembeli</th>
                            <th class="py-3 px-3">Total</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Bukti Bayar</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($recentOrders as $order)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="py-3.5 px-3 font-bold text-slate-900">
                                    #{{ $order->id_order }}
                                </td>
                                <td class="py-3.5 px-3 text-slate-500">
                                    {{ date('d M Y, H:i', strtotime($order->tgl_order)) }}
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-slate-800">
                                    {{ $order->pembeli->nama_user ?? 'Pembeli' }}
                                </td>
                                <td class="py-3.5 px-3 font-bold text-blue-600">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge_class }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3">
                                    @if ($order->bukti_bayar)
                                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600">
                                            <i class="fa-solid fa-file-circle-check"></i> Sudah Ada
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Belum Ada</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <a href="{{ route('penjual.order.show', $order->id_order) }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg font-bold transition-all">
                                        Periksa
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