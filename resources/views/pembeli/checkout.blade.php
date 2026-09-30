@extends('layouts.app')

@section('title', 'Checkout & Pengiriman - MarketPlace Sederhana')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Checkout -->
    <div class="pb-6 mb-8 border-b border-pink-100">
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
            <a href="{{ route('keranjang.index') }}" class="hover:text-pink-600 font-medium">Keranjang Belanja</a>
            <i class="fa-solid fa-chevron-right text-[10px] text-pink-300"></i>
            <span class="font-bold text-slate-800">Checkout & Informasi Pengiriman</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
            <span>Formulir Pesanan (Checkout)</span>
            <span class="w-2 h-2 rounded-full bg-pink-500"></span>
        </h1>
        <p class="text-sm text-slate-500 mt-1">Lengkapi alamat tujuan pengiriman untuk menyelesaikan pesanan Anda</p>
    </div>

    <!-- Form Checkout -->
    <form action="{{ route('pembeli.checkout.proses') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Kolom Kiri: Formulir Alamat Pengiriman -->
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white rounded-3xl border border-pink-100 p-6 sm:p-8 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 pb-4 border-b border-pink-50 flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-pink-600"></i> Informasi Alamat Pengiriman
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                        <!-- Nama Penerima -->
                        <div>
                            <label for="nama_penerima" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nama Penerima <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="nama_penerima" name="nama_penerima" 
                                   value="{{ old('nama_penerima', auth()->user()->nama_user) }}" required
                                   placeholder="Contoh: Siti Pembeli"
                                   class="w-full px-4 py-2.5 bg-pink-50/20 border @error('nama_penerima') border-rose-300 ring-1 ring-rose-300 @else border-pink-100 @enderror rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                            @error('nama_penerima')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Nomor Telepon -->
                        <div>
                            <label for="no_telepon" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                No. Telepon / WhatsApp <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="no_telepon" name="no_telepon" 
                                   value="{{ old('no_telepon') }}" required
                                   placeholder="Contoh: 08123456789"
                                   class="w-full px-4 py-2.5 bg-pink-50/20 border @error('no_telepon') border-rose-300 ring-1 ring-rose-300 @else border-pink-100 @enderror rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                            @error('no_telepon')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alamat Lengkap -->
                        <div class="sm:col-span-2">
                            <label for="alamat_lengkap" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Alamat Tujuan Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" required
                                      placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan, kota/kabupaten, kode pos"
                                      class="w-full px-4 py-2.5 bg-pink-50/20 border @error('alamat_lengkap') border-rose-300 ring-1 ring-rose-300 @else border-pink-100 @enderror rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">{{ old('alamat_lengkap') }}</textarea>
                            @error('alamat_lengkap')
                                <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Catatan Pesanan -->
                        <div class="sm:col-span-2">
                            <label for="catatan" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Catatan Khusus untuk Penjual (Opsional)
                            </label>
                            <input type="text" id="catatan" name="catatan" value="{{ old('catatan') }}"
                                   placeholder="Contoh: Warna hitam, titipkan ke satpam jika sedang keluar"
                                   class="w-full px-4 py-2.5 bg-pink-50/20 border border-pink-100 rounded-2xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="bg-white rounded-3xl border border-pink-100 p-6 sm:p-8 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900 pb-4 border-b border-pink-50 flex items-center gap-2">
                        <i class="fa-solid fa-credit-card text-pink-600"></i> Metode Pembayaran
                    </h3>
                    <div class="mt-4 p-4 rounded-2xl bg-pink-50/40 border border-pink-100 flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center font-bold text-lg flex-shrink-0">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <div class="text-xs text-slate-700 leading-relaxed">
                            <span class="font-bold text-slate-900 block text-sm">Transfer Bank / E-Wallet Manual</span>
                            Setelah pesanan dibuat, Anda akan menerima nomor rekening tujuan. Anda dapat langsung mengunggah foto bukti pembayaran pada halaman rincian pesanan.
                        </div>
                    </div>
                </div>

            </div>

            <!-- Kolom Kanan: Rincian Produk & Tombol Buat Pesanan -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl border border-pink-100 p-6 shadow-sm sticky top-24">
                    <h3 class="text-base font-black text-slate-900 pb-4 border-b border-pink-50">
                        Item Pesanan Anda
                    </h3>

                    <!-- List Item Singkat -->
                    <div class="divide-y divide-pink-50 max-h-64 overflow-y-auto pr-1">
                        @foreach ($cart as $item)
                            <div class="py-3 flex items-center gap-3">
                                <img src="{{ $item['gambar_product'] }}" alt="" class="w-12 h-12 rounded-xl object-cover bg-pink-50/50 flex-shrink-0 border border-pink-100">
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-xs font-bold text-slate-900 truncate">{{ $item['nama_product'] }}</h5>
                                    <p class="text-[11px] text-slate-500">{{ $item['qty'] }} &times; Rp {{ number_format($item['harga'], 0, ',', '.') }}</p>
                                </div>
                                <div class="text-xs font-bold text-slate-900">
                                    Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-pink-50 mt-4 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal Produk</span>
                            <span class="font-bold text-slate-900">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Ongkos Kirim</span>
                            <span class="font-bold text-emerald-600">Gratis Ongkir</span>
                        </div>
                        <div class="flex justify-between text-slate-900 text-sm font-black pt-3 border-t border-pink-50">
                            <span>Total Tagihan</span>
                            <span class="text-pink-600 text-base font-black">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-bold rounded-2xl text-sm shadow-md shadow-pink-500/25 hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-shield-check"></i>
                            <span>Buat Pesanan Sekarang</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-slate-400 text-center mt-3">
                        <i class="fa-solid fa-lock text-[10px] text-pink-400"></i> Transaksi aman & data pengiriman terlindungi
                    </p>
                </div>
            </div>

        </div>
    </form>

</div>
@endsection
