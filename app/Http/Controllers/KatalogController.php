<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

/**
 * Controller Katalog & Landing Page
 * Menampilkan etalase produk untuk calon pembeli maupun pengguna umum
 */
class KatalogController extends Controller
{
    /**
     * Menampilkan Landing Page dengan katalog produk, filter kategori, dan pencarian
     */
    public function index(Request $request)
    {
        $query = Produk::with('penjual');

        // Filter pencarian berdasarkan nama produk atau deskripsi
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_product', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi', 'like', "%{$keyword}%");
            });
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori') && $request->kategori !== 'Semua') {
            $query->where('kategori', $request->kategori);
        }

        // Ambil daftar kategori unik dari tabel produk
        $kategoriList = Produk::select('kategori')->distinct()->pluck('kategori');

        // Urutkan produk yang terbaru dan status tersedia di atas
        $produk = $query->orderByRaw("FIELD(status_product, 'tersedia', 'habis')")
                        ->orderByDesc('id_product')
                        ->paginate(8)
                        ->withQueryString();

        return view('landing', compact('produk', 'kategoriList'));
    }

    /**
     * Menampilkan halaman detail produk
     */
    public function show($id)
    {
        $produk = Produk::with('penjual')->findOrFail($id);

        // Rekomendasi produk terkait dengan kategori yang sama
        $produkTerkait = Produk::where('kategori', $produk->kategori)
            ->where('id_product', '!=', $produk->id_product)
            ->limit(4)
            ->get();

        return view('produk.detail', compact('produk', 'produkTerkait'));
    }
}
