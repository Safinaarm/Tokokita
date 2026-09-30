<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

/**
 * Controller Keranjang Belanja (Shopping Cart)
 * Mengelola keranjang belanja pembeli berbasis sesi
 */
class KeranjangController extends Controller
{
    /**
     * Menampilkan isi keranjang belanja
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        $totalHarga = 0;
        $totalItem = 0;

        foreach ($cart as $item) {
            $totalHarga += $item['harga'] * $item['qty'];
            $totalItem  += $item['qty'];
        }

        return view('keranjang.index', compact('cart', 'totalHarga', 'totalItem'));
    }

    /**
     * Menambahkan produk ke dalam keranjang
     */
    public function add(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        // Periksa apakah produk tersedia dan stok mencukupi
        if ($produk->status_product === 'habis' || $produk->stok <= 0) {
            return back()->with('error', 'Maaf, stok produk ini sedang habis.');
        }

        $qtyDiminta = (int) $request->input('qty', 1);
        if ($qtyDiminta < 1) {
            $qtyDiminta = 1;
        }

        $cart = session()->get('cart', []);

        // Jika produk sudah ada di keranjang, tambahkan jumlahnya
        if (isset($cart[$id])) {
            $newQty = $cart[$id]['qty'] + $qtyDiminta;
            if ($newQty > $produk->stok) {
                return back()->with('error', 'Jumlah melebihi stok yang tersedia (Sisa stok: ' . $produk->stok . ').');
            }
            $cart[$id]['qty'] = $newQty;
        } else {
            // Jika belum ada, masukkan sebagai item baru
            if ($qtyDiminta > $produk->stok) {
                return back()->with('error', 'Jumlah melebihi stok yang tersedia (Sisa stok: ' . $produk->stok . ').');
            }

            $cart[$id] = [
                'id_product'     => $produk->id_product,
                'nama_product'   => $produk->nama_product,
                'harga'          => (float) $produk->harga,
                'qty'            => $qtyDiminta,
                'gambar_product' => $produk->gambar_url,
                'stok'           => $produk->stok,
                'kategori'       => $produk->kategori,
                'penjual_id'     => $produk->penjual_id,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('keranjang.index')->with('success', 'Produk berhasil ditambahkan ke keranjang belanja!');
    }

    /**
     * Memperbarui kuantitas produk dalam keranjang
     */
    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $action = $request->input('action');
            $produk = Produk::find($id);

            if ($action === 'increase') {
                if ($produk && $cart[$id]['qty'] < $produk->stok) {
                    $cart[$id]['qty']++;
                } else {
                    return back()->with('error', 'Stok produk tidak mencukupi.');
                }
            } elseif ($action === 'decrease') {
                if ($cart[$id]['qty'] > 1) {
                    $cart[$id]['qty']--;
                } else {
                    unset($cart[$id]);
                }
            } elseif ($request->filled('qty')) {
                $qty = (int) $request->qty;
                if ($qty <= 0) {
                    unset($cart[$id]);
                } else {
                    if ($produk && $qty > $produk->stok) {
                        return back()->with('error', 'Stok hanya tersisa ' . $produk->stok . ' item.');
                    }
                    $cart[$id]['qty'] = $qty;
                }
            }

            session()->put('cart', $cart);
        }

        return redirect()->route('keranjang.index')->with('success', 'Keranjang berhasil diperbarui.');
    }

    /**
     * Menghapus salah satu item dari keranjang
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('keranjang.index')->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    /**
     * Mengosongkan seluruh keranjang belanja
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('keranjang.index')->with('info', 'Keranjang belanja telah dikosongkan.');
    }
}
