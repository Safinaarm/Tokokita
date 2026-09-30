<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Controller Manajemen Produk Penjual
 * Menangani CRUD katalog produk, upload foto, pengelolaan kategori, stok, dan status
 */
class PenjualProdukController extends Controller
{
    /**
     * Menampilkan daftar semua produk milik penjual
     */
    public function index(Request $request)
    {
        $penjualId = Auth::user()->id_user;
        $query = Produk::where('penjual_id', $penjualId);

        // Filter pencarian
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_product', 'like', "%{$keyword}%")
                  ->orWhere('kategori', 'like', "%{$keyword}%");
            });
        }

        // Filter status produk
        if ($request->filled('status') && in_array($request->status, ['tersedia', 'habis'])) {
            $query->where('status_product', $request->status);
        }

        $produkList = $query->orderByDesc('id_product')->paginate(10)->withQueryString();

        return view('penjual.produk.index', compact('produkList'));
    }

    /**
     * Menampilkan formulir tambah produk baru
     */
    public function create()
    {
        return view('penjual.produk.create');
    }

    /**
     * Menyimpan produk baru ke katalog
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_product'   => 'required|string|max:150',
            'kategori'       => 'required|string|max:50',
            'harga'          => 'required|numeric|min:0',
            'stok'           => 'required|integer|min:0',
            'deskripsi'      => 'nullable|string',
            'status_product' => 'required|in:tersedia,habis',
            'gambar_product' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'nama_product.required'   => 'Nama produk wajib diisi.',
            'kategori.required'       => 'Kategori produk wajib diisi.',
            'harga.required'          => 'Harga produk wajib diisi.',
            'harga.numeric'           => 'Harga harus berupa angka.',
            'stok.required'           => 'Jumlah stok wajib diisi.',
            'gambar_product.image'    => 'Berkas gambar harus berupa format gambar valid (JPG, PNG, WEBP).',
            'gambar_product.max'      => 'Ukuran gambar maksimal 3MB.',
        ]);

        // Upload gambar jika disediakan
        $gambarPath = null;
        if ($request->hasFile('gambar_product')) {
            $gambarPath = $request->file('gambar_product')->store('produk', 'public');
        }

        // Jika stok 0, otomatis status produk habis
        $statusProduct = ($data['stok'] <= 0) ? 'habis' : $data['status_product'];

        Produk::create([
            'penjual_id'     => Auth::user()->id_user,
            'nama_product'   => $data['nama_product'],
            'kategori'       => $data['kategori'],
            'harga'          => $data['harga'],
            'stok'           => $data['stok'],
            'deskripsi'      => $data['deskripsi'] ?? null,
            'gambar_product' => $gambarPath,
            'status_product' => $statusProduct,
        ]);

        return redirect()->route('penjual.produk.index')->with('success', 'Produk baru berhasil ditambahkan ke katalog!');
    }

    /**
     * Menampilkan formulir edit produk
     */
    public function edit($id)
    {
        $produk = Produk::where('penjual_id', Auth::user()->id_user)->findOrFail($id);
        return view('penjual.produk.edit', compact('produk'));
    }

    /**
     * Memperbarui informasi produk
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::where('penjual_id', Auth::user()->id_user)->findOrFail($id);

        $data = $request->validate([
            'nama_product'   => 'required|string|max:150',
            'kategori'       => 'required|string|max:50',
            'harga'          => 'required|numeric|min:0',
            'stok'           => 'required|integer|min:0',
            'deskripsi'      => 'nullable|string',
            'status_product' => 'required|in:tersedia,habis',
            'gambar_product' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        // Jika ada unggahan gambar baru
        if ($request->hasFile('gambar_product')) {
            // Hapus file lama di storage jika bukan URL eksternal
            if ($produk->gambar_product && Storage::disk('public')->exists($produk->gambar_product)) {
                Storage::disk('public')->delete($produk->gambar_product);
            }
            $data['gambar_product'] = $request->file('gambar_product')->store('produk', 'public');
        }

        // Koreksi otomatis jika stok 0
        if ($data['stok'] <= 0) {
            $data['status_product'] = 'habis';
        }

        $produk->update($data);

        return redirect()->route('penjual.produk.index')->with('success', 'Produk "' . $produk->nama_product . '" berhasil diperbarui!');
    }

    /**
     * Cepat mengubah status produk antara Tersedia atau Habis
     */
    public function toggleStatus($id)
    {
        $produk = Produk::where('penjual_id', Auth::user()->id_user)->findOrFail($id);

        $produk->status_product = ($produk->status_product === 'tersedia') ? 'habis' : 'tersedia';
        $produk->save();

        return back()->with('success', 'Status produk diubah menjadi: ' . strtoupper($produk->status_product));
    }

    /**
     * Menghapus produk dari katalog
     */
    public function destroy($id)
    {
        $produk = Produk::where('penjual_id', Auth::user()->id_user)->findOrFail($id);

        // Periksa apakah produk sudah pernah dipesan pada detail_order
        if ($produk->detailOrders()->exists()) {
            return back()->with('error', 'Produk ini tidak dapat dihapus karena sudah memiliki riwayat transaksi/order. Anda dapat mengubah statusnya menjadi "Habis".');
        }

        // Hapus file gambar dari storage
        if ($produk->gambar_product && Storage::disk('public')->exists($produk->gambar_product)) {
            Storage::disk('public')->delete($produk->gambar_product);
        }

        $produk->delete();

        return redirect()->route('penjual.produk.index')->with('success', 'Produk berhasil dihapus dari katalog.');
    }
}
