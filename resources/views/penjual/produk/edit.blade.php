@extends('layouts.app')

@section('title', 'Edit Produk: ' . $produk->nama_product . ' - Panel Penjual')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Navigasi Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('penjual.produk.index') }}" class="hover:text-blue-600">Manajemen Produk</a>
        <i class="fa-solid fa-chevron-right text-[10px]"></i>
        <span class="font-bold text-slate-800">Edit Produk</span>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 shadow-sm">
        
        <div class="pb-6 mb-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Edit Data Produk</h1>
                <p class="text-sm text-slate-500 mt-1">Perbarui rincian informasi dan status produk pada etalase toko</p>
            </div>
            <a href="{{ route('produk.detail', $produk->id_product) }}" target="_blank" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl inline-flex items-center gap-1.5 self-start">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat di Katalog
            </a>
        </div>

        <form action="{{ route('penjual.produk.update', $produk->id_product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                
                <!-- Nama Produk -->
                <div class="sm:col-span-2">
                    <label for="nama_product" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Produk <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="nama_product" name="nama_product" value="{{ old('nama_product', $produk->nama_product) }}" required
                           class="w-full px-4 py-2.5 bg-slate-50 border @error('nama_product') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    @error('nama_product')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kategori Produk -->
                <div>
                    <label for="kategori" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Kategori Produk <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="kategori" name="kategori" value="{{ old('kategori', $produk->kategori) }}" required list="listKategori"
                           class="w-full px-4 py-2.5 bg-slate-50 border @error('kategori') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    <datalist id="listKategori">
                        <option value="Pakaian">
                        <option value="Sepatu">
                        <option value="Tas">
                        <option value="Elektronik">
                        <option value="Aksesoris">
                        <option value="Kecantikan">
                        <option value="Makanan & Minuman">
                    </datalist>
                    @error('kategori')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Harga Satuan -->
                <div>
                    <label for="harga" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Harga (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 font-bold text-xs">
                            Rp
                        </div>
                        <input type="number" id="harga" name="harga" value="{{ old('harga', (int)$produk->harga) }}" required min="0" step="500"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border @error('harga') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    </div>
                    @error('harga')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah Stok -->
                <div>
                    <label for="stok" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Jumlah Stok Tersedia <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="stok" name="stok" value="{{ old('stok', $produk->stok) }}" required min="0"
                           class="w-full px-4 py-2.5 bg-slate-50 border @error('stok') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
                    @error('stok')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Ketersediaan -->
                <div>
                    <label for="status_product" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Status Ketersediaan <span class="text-rose-500">*</span>
                    </label>
                    <select id="status_product" name="status_product" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="tersedia" {{ old('status_product', $produk->status_product) === 'tersedia' ? 'selected' : '' }}>Tersedia (Siap Dijual)</option>
                        <option value="habis" {{ old('status_product', $produk->status_product) === 'habis' ? 'selected' : '' }}>Habis (Stok Kosong)</option>
                    </select>
                </div>

                <!-- Preview & Unggah Gambar Produk -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Foto Produk Saat Ini
                    </label>
                    <div class="flex items-center gap-4 mb-3">
                        <img src="{{ $produk->gambar_url }}" alt="Preview" class="w-20 h-20 rounded-2xl object-cover bg-slate-100 border border-slate-200">
                        <div class="text-xs text-slate-500">
                            Unggah berkas baru jika ingin mengganti gambar produk di atas.
                        </div>
                    </div>

                    <input type="file" id="gambar_product" name="gambar_product" accept="image/*"
                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    @error('gambar_product')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Produk -->
                <div class="sm:col-span-2">
                    <label for="deskripsi" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Deskripsi Lengkap Produk
                    </label>
                    <textarea id="deskripsi" name="deskripsi" rows="4"
                              class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
                </div>

            </div>

            <!-- Tombol Aksi Simpan & Batal -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('penjual.produk.index') }}" class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>

    </div>

</div>
@endsection
