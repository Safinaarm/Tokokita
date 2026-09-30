@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->id_order . ' - Panel Penjual')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Navigasi Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('penjual.order.index') }}" class="hover:text-blue-600">Manajemen Pesanan</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="font-bold text-slate-800">Verifikasi Pesanan #{{ $order->id_order }}</span>
    </div>

    <!-- Header Status Order -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pesanan Masuk #{{ $order->id_order }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $order->status_badge_class }}">
                    {{ $order->status_label }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Waktu Transaksi: {{ date('d F Y, H:i:s', strtotime($order->tgl_order)) }} WIB
            </p>
        </div>

        <!-- Tombol Aksi Cepat Konfirmasi Terima / Tolak jika status menunggu_konfirmasi -->
        @if ($order->status_order === 'menunggu_konfirmasi')
            <div class="flex items-center gap-2">
                <form action="{{ route('penjual.order.status', $order->id_order) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status_order" value="diproses">
                    <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>Terima & Proses Pesanan</span>
                    </button>
                </form>

                <form action="{{ route('penjual.order.status', $order->id_order) }}" method="POST" onsubmit="return confirm('Tolak pesanan ini dan batalkan transaksi?')">
                    @csrf
                    <input type="hidden" name="status_order" value="dibatalkan">
                    <button type="submit" class="px-4 py-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-xs font-bold border border-rose-200 transition-all flex items-center gap-1.5">
                        <i class="fa-solid fa-xmark"></i>
                        <span>Tolak Pesanan</span>
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Rincian Produk & Data Pembeli -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Tabel Item Produk -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm overflow-hidden">
                <h3 class="text-base font-bold text-slate-900 pb-4 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-blue-600"></i> Produk yang Dipesan
                </h3>

                <div class="divide-y divide-slate-100 mt-2">
                    @foreach ($order->details as $item)
                        <div class="py-4 flex flex-col sm:flex-row items-center gap-4">
                            <img src="{{ $item->produk->gambar_url ?? '' }}" alt="" class="w-16 h-16 rounded-xl object-cover bg-slate-100 border border-slate-100 flex-shrink-0">
                            
                            <div class="flex-1 text-center sm:text-left">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $item->produk->kategori ?? 'Kategori' }}</span>
                                <h4 class="font-bold text-slate-900 text-sm">
                                    {{ $item->produk->nama_product ?? 'Produk telah dihapus' }}
                                </h4>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    Harga: Rp {{ number_format($item->harga, 0, ',', '.') }} &times; {{ $item->qty }} item
                                </div>
                            </div>

                            <div class="text-right font-extrabold text-sm text-slate-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Total Rincian -->
                <div class="pt-4 border-t border-slate-100 mt-2 space-y-2 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Total Nilai Produk</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-900 text-base font-black pt-3 border-t border-slate-100">
                        <span>Total Tagihan Pesanan</span>
                        <span class="text-blue-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Informasi Pembeli & Alamat Pengiriman -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-user-check text-blue-600"></i> Informasi Pelanggan & Alamat Kirim
                </h3>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-4">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block mb-0.5">Nama Akun Pembeli</span>
                        <strong class="text-slate-900">{{ $order->pembeli->nama_user ?? 'Tidak Diketahui' }}</strong>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                        <span class="text-slate-400 block mb-0.5">Email Pembeli</span>
                        <strong class="text-slate-900">{{ $order->pembeli->email ?? '-' }}</strong>
                    </div>
                </div>

                <div>
                    <span class="text-xs font-bold text-slate-700 block mb-1">Alamat Tujuan Pengiriman:</span>
                    <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        {{ $order->alamat_pengiriman }}
                    </div>
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Verifikasi Bukti Pembayaran & Pengubah Status -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Verifikasi Bukti Pembayaran -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-blue-600"></i> Verifikasi Bukti Bayar
                </h3>

                @if ($order->bukti_bayar)
                    <div class="mt-4 space-y-3">
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>Pembeli telah mengunggah bukti pembayaran</span>
                        </div>

                        <!-- Gambar Bukti -->
                        <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-100">
                            <a href="{{ asset('storage/' . $order->bukti_bayar) }}" target="_blank" title="Klik untuk memperbesar">
                                <img src="{{ asset('storage/' . $order->bukti_bayar) }}" alt="Bukti Transfer" class="w-full h-52 object-contain hover:scale-102 transition-transform cursor-zoom-in">
                            </a>
                        </div>
                        <p class="text-[11px] text-slate-400 text-center italic">Klik gambar di atas untuk membuka ukuran asli</p>
                    </div>
                @else
                    <div class="mt-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        <span>Pembeli belum mengunggah berkas bukti pembayaran.</span>
                    </div>
                @endif
            </div>

            <!-- Perbarui Status Pesanan -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-blue-600"></i> Perbarui Status Pesanan
                </h3>

                <form action="{{ route('penjual.order.status', $order->id_order) }}" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label for="status_order" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Status Terkini
                        </label>
                        <select id="status_order" name="status_order" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="menunggu_bayar" {{ $order->status_order === 'menunggu_bayar' ? 'selected' : '' }}>Menunggu Bayar</option>
                            <option value="menunggu_konfirmasi" {{ $order->status_order === 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                            <option value="diproses" {{ $order->status_order === 'diproses' ? 'selected' : '' }}>Diproses (Pesanan Diterima)</option>
                            <option value="dikirim" {{ $order->status_order === 'dikirim' ? 'selected' : '' }}>Dikirim (Menuju Pembeli)</option>
                            <option value="selesai" {{ $order->status_order === 'selesai' ? 'selected' : '' }}>Selesai (Diterima Pembeli)</option>
                            <option value="dibatalkan" {{ $order->status_order === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan (Tolak Pesanan)</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Status Baru</span>
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
