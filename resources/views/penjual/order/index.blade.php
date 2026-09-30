@extends('layouts.app')

@section('title', 'Manajemen Pesanan Masuk - Panel Penjual')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Manajemen Order -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Pesanan Masuk</h1>
            <p class="text-sm text-slate-500 mt-1">Verifikasi pembayaran, konfirmasi transaksi, dan perbarui status pengiriman</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('penjual.dashboard') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                <i class="fa-solid fa-chart-pie mr-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Tab Status -->
    <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 scrollbar-none">
        @php
            $currentStatus = request('status', 'semua');
            $statuses = [
                'semua'               => 'Semua Pesanan',
                'menunggu_konfirmasi' => 'Perlu Konfirmasi',
                'menunggu_bayar'      => 'Menunggu Bayar',
                'diproses'            => 'Diproses',
                'dikirim'             => 'Dikirim',
                'selesai'             => 'Selesai',
                'dibatalkan'          => 'Dibatalkan',
            ];
        @endphp

        @foreach ($statuses as $key => $label)
            <a href="{{ route('penjual.order.index', array_merge(request()->except('status', 'page'), ['status' => $key])) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $currentStatus === $key ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Tabel Daftar Pesanan Masuk -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @if ($orders->isEmpty())
            <div class="text-center py-16 p-8">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-3 text-2xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">Tidak ada pesanan pada kategori ini</h4>
                <p class="text-xs text-slate-500 mt-1">Saat ada pesanan baru masuk dari pembeli, pesanan akan ditampilkan di sini.</p>
                <a href="{{ route('penjual.order.index') }}" class="inline-block mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl">
                    Reset Filter
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4">No. Order & Tanggal</th>
                            <th class="py-3.5 px-4">Pembeli</th>
                            <th class="py-3.5 px-4">Rincian Item</th>
                            <th class="py-3.5 px-4">Total</th>
                            <th class="py-3.5 px-4">Bukti Bayar</th>
                            <th class="py-3.5 px-4">Status Pesanan</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-slate-50 transition-colors">
                                
                                <!-- No Order & Tanggal -->
                                <td class="py-3.5 px-4">
                                    <div class="font-black text-slate-900 text-sm">#{{ $order->id_order }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ date('d M Y, H:i', strtotime($order->tgl_order)) }}
                                    </div>
                                </td>

                                <!-- Pembeli -->
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-800">{{ $order->pembeli->nama_user ?? 'User Dihapus' }}</div>
                                    <div class="text-[10px] text-slate-400 truncate max-w-[130px]">{{ $order->pembeli->email ?? '-' }}</div>
                                </td>

                                <!-- Rincian Item -->
                                <td class="py-3.5 px-4">
                                    @foreach ($order->details as $item)
                                        <div class="text-slate-700 font-medium truncate max-w-[180px]">
                                            &bull; {{ $item->produk->nama_product ?? 'Item' }} <span class="text-slate-400">({{ $item->qty }}x)</span>
                                        </div>
                                    @endforeach
                                </td>

                                <!-- Total Harga -->
                                <td class="py-3.5 px-4 font-bold text-blue-600 text-sm">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>

                                <!-- Bukti Pembayaran -->
                                <td class="py-3.5 px-4">
                                    @if ($order->bukti_bayar)
                                        <a href="{{ asset('storage/' . $order->bukti_bayar) }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold text-[11px] hover:bg-emerald-100 transition-colors" title="Klik untuk melihat bukti transfer">
                                            <i class="fa-solid fa-receipt"></i>
                                            <span>Lihat Bukti</span>
                                        </a>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Belum diunggah</span>
                                    @endif
                                </td>

                                <!-- Badge Status -->
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->status_badge_class }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>

                                <!-- Tombol Aksi -->
                                <td class="py-3.5 px-4 text-right">
                                    <a href="{{ route('penjual.order.show', $order->id_order) }}" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all inline-flex items-center gap-1.5 shadow-sm">
                                        <span>Kelola</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100 flex justify-center">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
