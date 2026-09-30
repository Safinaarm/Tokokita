@extends('layouts.app')

@section('title', 'Manajemen Produk - MarketPlace Sederhana')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header Manajemen Produk -->
    <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 mb-6 border-b border-slate-200 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Katalog Produk</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data produk, harga, stok, foto, dan status ketersediaan</p>
        </div>

        <div>
            <a href="{{ route('penjual.produk.create') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg transition-all inline-flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Produk Baru
            </a>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 mb-6 shadow-sm">
        <form action="{{ route('penjual.produk.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama produk atau kategori..."
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <option value="">Semua Status</option>
                    <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                    <option value="habis" {{ request('status') === 'habis' ? 'selected' : '' }}>Habis</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-sm">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Produk -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        @if ($produkList->isEmpty())
            <div class="text-center py-16 p-8">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400 mb-3 text-2xl">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h4 class="text-base font-bold text-slate-800">Tidak ada produk ditemukan</h4>
                <p class="text-xs text-slate-500 mt-1">Mulai tambahkan produk baru ke dalam etalase toko Anda.</p>
                <a href="{{ route('penjual.produk.create') }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold">
                    Tambah Produk Sekarang
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3.5 px-4">Produk</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4">Harga</th>
                            <th class="py-3.5 px-4">Stok</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-4 text-center">Toggle Status</th>
                            <th class="py-3.5 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($produkList as $item)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <!-- Info Produk & Gambar -->
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $item->gambar_url }}" alt="" class="w-12 h-12 rounded-xl object-cover bg-slate-100 border border-slate-200 flex-shrink-0">
                                        <div>
                                            <a href="{{ route('produk.detail', $item->id_product) }}" target="_blank" class="font-bold text-slate-900 hover:text-blue-600 line-clamp-1">
                                                {{ $item->nama_product }}
                                            </a>
                                            <span class="text-[10px] text-slate-400 font-mono">ID: #{{ $item->id_product }}</span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Kategori -->
                                <td class="py-3.5 px-4 font-semibold text-slate-600">
                                    <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-[11px]">
                                        {{ $item->kategori }}
                                    </span>
                                </td>

                                <!-- Harga -->
                                <td class="py-3.5 px-4 font-bold text-blue-600 text-sm">
                                    {{ $item->harga_rupiah }}
                                </td>

                                <!-- Stok -->
                                <td class="py-3.5 px-4">
                                    @if ($item->stok > 0)
                                        <span class="font-bold text-slate-800">{{ $item->stok }} item</span>
                                    @else
                                        <span class="font-bold text-rose-600">0 (Habis)</span>
                                    @endif
                                </td>

                                <!-- Badge Status -->
                                <td class="py-3.5 px-4">
                                    @if ($item->status_product === 'tersedia' && $item->stok > 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Tersedia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Habis
                                        </span>
                                    @endif
                                </td>

                                <!-- Quick Toggle Status -->
                                <td class="py-3.5 px-4 text-center">
                                    <form action="{{ route('penjual.produk.toggle', $item->id_product) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition-all {{ $item->status_product === 'tersedia' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}"
                                                title="Klik untuk mengubah status produk">
                                            {{ $item->status_product === 'tersedia' ? 'Ubah ke Habis' : 'Ubah ke Tersedia' }}
                                        </button>
                                    </form>
                                </td>

                                <!-- Aksi Edit & Hapus -->
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('penjual.produk.edit', $item->id_product) }}" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Informasi">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>

                                        <form action="{{ route('penjual.produk.destroy', $item->id_product) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini dari katalog?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Produk">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 border-t border-slate-100 flex justify-center">
                {{ $produkList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
