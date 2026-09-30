@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->id_order . ' - MarketPlace Sederhana')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Navigasi Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('pembeli.pesanan.index') }}" class="hover:text-blue-600">Daftar Pesanan</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="font-bold text-slate-800">Detail Pesanan #{{ $order->id_order }}</span>
    </div>

    <!-- Header Status Order -->
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 shadow-sm mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pesanan #{{ $order->id_order }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $order->status_badge_class }}">
                    {{ $order->status_label }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Dibuat pada {{ date('d F Y, H:i:s', strtotime($order->tgl_order)) }} WIB
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($order->status_order === 'dikirim')
                <form action="{{ route('pembeli.pesanan.selesai', $order->id_order) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa barang sudah diterima?')">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-500/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Konfirmasi Terima Barang</span>
                    </button>
                </form>
            @endif

            @if ($order->status_order === 'menunggu_bayar')
                <form action="{{ route('pembeli.pesanan.batal', $order->id_order) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-rose-200">
                        <i class="fa-solid fa-ban"></i>
                        <span>Batalkan Pesanan</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Rincian Produk & Pengiriman -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Tabel Item Produk -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm overflow-hidden">
                <h3 class="text-base font-bold text-slate-900 pb-4 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-blue-600"></i> Rincian Produk yang Dipesan
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
                                    {{ $item->qty }} item &times; Rp {{ number_format($item->harga, 0, ',', '.') }}
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
                        <span>Total Nilai Barang</span>
                        <span class="font-bold text-slate-800">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Ongkos Kirim</span>
                        <span class="font-bold text-emerald-600">Gratis</span>
                    </div>
                    <div class="flex justify-between text-slate-900 text-base font-black pt-3 border-t border-slate-100">
                        <span>Total Pembayaran</span>
                        <span class="text-blue-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Informasi Alamat Pengiriman -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-truck-fast text-blue-600"></i> Tujuan Pengiriman
                </h3>
                <div class="mt-3 text-xs text-slate-700 leading-relaxed whitespace-pre-line bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    {{ $order->alamat_pengiriman }}
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Pembayaran & Unggah Bukti Transfer -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Card Instruksi Rekening Pembayaran -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-wallet text-blue-600"></i> Rekening Pembayaran
                </h3>

                <p class="text-xs text-slate-500 mt-3">Silakan transfer sesuai total nominal berikut ke rekening resmi:</p>

                <div class="my-4 p-4 rounded-2xl bg-blue-50 border border-blue-200/80">
                    <div class="text-[10px] text-blue-600 uppercase font-bold tracking-wider">Bank BCA (Virtual Account / Rekening)</div>
                    <div class="text-lg font-mono font-bold text-slate-900 mt-1 select-all">8801 2345 6789 00</div>
                    <div class="text-xs text-slate-600 mt-0.5">a.n. <strong>Marketplace TokoKita</strong></div>
                </div>

                <div class="text-xs text-slate-600 flex justify-between py-1">
                    <span>Jumlah yang harus dibayar:</span>
                    <span class="font-extrabold text-blue-600 text-sm">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Card Upload & Tampilan Bukti Bayar -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice-dollar text-blue-600"></i> Bukti Pembayaran
                </h3>

                @if ($order->bukti_bayar)
                    <!-- Bukti Telah Diunggah -->
                    <div class="mt-4 space-y-3">
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs flex items-center gap-2">
                            <i class="fa-solid fa-check-circle text-emerald-600"></i>
                            <span>Bukti transfer telah diunggah</span>
                        </div>

                        <!-- Gambar Bukti -->
                        <div class="rounded-2xl overflow-hidden border border-slate-200 bg-slate-100">
                            <a href="{{ asset('storage/' . $order->bukti_bayar) }}" target="_blank">
                                <img src="{{ asset('storage/' . $order->bukti_bayar) }}" alt="Bukti Transfer" class="w-full h-48 object-contain hover:opacity-90 transition-opacity">
                            </a>
                        </div>
                        <p class="text-[10px] text-slate-400 text-center italic">Klik gambar untuk melihat ukuran penuh</p>

                        <!-- Pilihan Unggah Ulang jika masih dalam status verifikasi -->
                        @if (in_array($order->status_order, ['menunggu_bayar', 'menunggu_konfirmasi']))
                            <div x-data="{ reupload: false }" class="pt-2">
                                <button type="button" @click="reupload = !reupload" class="text-xs text-blue-600 hover:underline font-semibold block text-center w-full">
                                    <span x-show="!reupload">Ganti Bukti Pembayaran</span>
                                    <span x-show="reupload">Tutup Form Ganti</span>
                                </button>

                                <form x-show="reupload" action="{{ route('pembeli.pesanan.bukti', $order->id_order) }}" method="POST" enctype="multipart/form-data" class="mt-3 space-y-3">
                                    @csrf
                                    <input type="file" name="bukti_bayar" accept="image/*" required class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-sm">
                                        Perbarui Bukti
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Form Upload Bukti Baru -->
                    <div class="mt-4">
                        @if ($order->status_order === 'menunggu_bayar')
                            <p class="text-xs text-slate-600 mb-3">
                                Silakan foto struk transfer ATM / m-Banking Anda dan unggah berkasnya di bawah ini:
                            </p>

                            <form action="{{ route('pembeli.pesanan.bukti', $order->id_order) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                @csrf
                                <div>
                                    <input type="file" name="bukti_bayar" accept="image/*" required
                                           class="block w-full text-xs text-slate-500 file:mr-2 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @error('bukti_bayar')
                                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-md shadow-blue-500/20 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <span>Unggah Bukti Transfer</span>
                                </button>
                            </form>
                        @else
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-500 text-xs text-center">
                                Status pesanan: {{ $order->status_label }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>

    </div>

</div>
@endsection
