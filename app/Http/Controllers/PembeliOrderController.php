<?php

namespace App\Http\Controllers;

use App\Models\DetailOrder;
use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Controller Transaksi Order Pembeli
 * Menangani checkout, pembuatan pesanan baru, upload bukti transfer,
 * serta pelacakan status pesanan
 */
class PembeliOrderController extends Controller
{
    /**
     * Menampilkan halaman Checkout
     */
    public function showCheckout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('landing')->with('error', 'Keranjang belanja Anda masih kosong. Silakan pilih produk terlebih dahulu.');
        }

        $totalHarga = 0;
        foreach ($cart as $item) {
            $totalHarga += $item['harga'] * $item['qty'];
        }

        return view('pembeli.checkout', compact('cart', 'totalHarga'));
    }

    /**
     * Memproses pesanan baru (Checkout Store)
     * Mengurangi stok produk dan mencatat detail order
     */
    public function prosesCheckout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('landing')->with('error', 'Keranjang belanja kosong.');
        }

        // Validasi input data pengiriman
        $request->validate([
            'nama_penerima'    => 'required|string|max:100',
            'no_telepon'       => 'required|string|max:20',
            'alamat_lengkap'   => 'required|string',
            'catatan'          => 'nullable|string|max:255',
        ], [
            'nama_penerima.required'  => 'Nama penerima wajib diisi.',
            'no_telepon.required'     => 'Nomor telepon/WhatsApp aktif wajib diisi.',
            'alamat_lengkap.required' => 'Alamat pengiriman lengkap wajib diisi.',
        ]);

        // Format alamat pengiriman lengkap
        $alamatPengiriman = "Penerima: {$request->nama_penerima} ({$request->no_telepon})\nAlamat: {$request->alamat_lengkap}";
        if ($request->filled('catatan')) {
            $alamatPengiriman .= "\nCatatan: {$request->catatan}";
        }

        // Hitung total harga dan validasi stok
        $totalHarga = 0;
        foreach ($cart as $id => $item) {
            $produk = Produk::find($id);
            if (!$produk || $produk->stok < $item['qty']) {
                return redirect()->route('keranjang.index')->with('error', 'Maaf, stok produk "' . ($produk->nama_product ?? 'Item') . '" tidak mencukupi.');
            }
            $totalHarga += $item['harga'] * $item['qty'];
        }

        // Simpan transaksi menggunakan Database Transaction agar konsisten
        $order = DB::transaction(function () use ($cart, $totalHarga, $alamatPengiriman) {
            // 1. Buat header order
            $newOrder = Order::create([
                'pembeli_id'        => Auth::user()->id_user,
                'tgl_order'         => now(),
                'total_harga'       => $totalHarga,
                'status_order'      => 'menunggu_bayar',
                'alamat_pengiriman' => $alamatPengiriman,
                'bukti_bayar'       => null,
            ]);

            // 2. Simpan setiap item ke detail_order & kurangi stok produk
            foreach ($cart as $id => $item) {
                DetailOrder::create([
                    'order_id'   => $newOrder->id_order,
                    'product_id' => $item['id_product'],
                    'qty'        => $item['qty'],
                    'harga'      => $item['harga'],
                    'subtotal'   => $item['harga'] * $item['qty'],
                ]);

                // Kurangi stok produk
                $produk = Produk::find($item['id_product']);
                if ($produk) {
                    $produk->stok -= $item['qty'];
                    if ($produk->stok <= 0) {
                        $produk->stok = 0;
                        $produk->status_product = 'habis';
                    }
                    $produk->save();
                }
            }

            return $newOrder;
        });

        // Kosongkan keranjang setelah order berhasil dibuat
        session()->forget('cart');

        return redirect()->route('pembeli.pesanan.detail', $order->id_order)
            ->with('success', 'Pesanan #' . $order->id_order . ' berhasil dibuat! Silakan lakukan pembayaran dan unggah bukti transfer.');
    }

    /**
     * Menampilkan daftar semua pesanan milik pembeli yang sedang login
     */
    public function daftarPesanan()
    {
        $orders = Order::where('pembeli_id', Auth::user()->id_user)
            ->with(['details.produk'])
            ->orderByDesc('id_order')
            ->paginate(10);

        return view('pembeli.pesanan', compact('orders'));
    }

    /**
     * Menampilkan detail pesanan pembeli tertentu & form upload bukti bayar
     */
    public function detailPesanan($id)
    {
        $order = Order::where('pembeli_id', Auth::user()->id_user)
            ->with(['details.produk.penjual'])
            ->findOrFail($id);

        return view('pembeli.pesanan_detail', compact('order'));
    }

    /**
     * Mengunggah bukti pembayaran untuk order tertentu
     */
    public function uploadBuktiBayar(Request $request, $id)
    {
        $order = Order::where('pembeli_id', Auth::user()->id_user)->findOrFail($id);

        $request->validate([
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'bukti_bayar.required' => 'Silakan pilih file bukti pembayaran.',
            'bukti_bayar.image'    => 'File harus berupa gambar (JPG, PNG, atau WEBP).',
            'bukti_bayar.max'      => 'Ukuran gambar maksimal 3MB.',
        ]);

        if ($request->hasFile('bukti_bayar')) {
            // Hapus file bukti lama jika ada
            if ($order->bukti_bayar && Storage::disk('public')->exists($order->bukti_bayar)) {
                Storage::disk('public')->delete($order->bukti_bayar);
            }

            $path = $request->file('bukti_bayar')->store('bukti_bayar', 'public');

            $order->update([
                'bukti_bayar'  => $path,
                'status_order' => 'menunggu_konfirmasi',
            ]);

            return back()->with('success', 'Bukti pembayaran berhasil diunggah! Status pesanan kini Menunggu Konfirmasi dari Penjual.');
        }

        return back()->with('error', 'Gagal mengunggah berkas.');
    }

    /**
     * Konfirmasi penerimaan pesanan oleh pembeli saat barang telah tiba
     */
    public function konfirmasiSelesai($id)
    {
        $order = Order::where('pembeli_id', Auth::user()->id_user)->findOrFail($id);

        if ($order->status_order !== 'dikirim') {
            return back()->with('error', 'Pesanan hanya dapat diselesaikan jika statusnya sedang dikirim.');
        }

        $order->update(['status_order' => 'selesai']);

        return back()->with('success', 'Terima kasih! Pesanan Anda telah selesai.');
    }

    /**
     * Batalkan pesanan (hanya bisa dibatalkan jika masih menunggu pembayaran)
     */
    public function batalkanPesanan($id)
    {
        $order = Order::where('pembeli_id', Auth::user()->id_user)->findOrFail($id);

        if ($order->status_order !== 'menunggu_bayar') {
            return back()->with('error', 'Pesanan yang sudah diproses tidak dapat dibatalkan secara sepihak.');
        }

        DB::transaction(function () use ($order) {
            // Kembalikan stok produk
            foreach ($order->details as $item) {
                $produk = Produk::find($item->product_id);
                if ($produk) {
                    $produk->stok += $item->qty;
                    if ($produk->status_product === 'habis' && $produk->stok > 0) {
                        $produk->status_product = 'tersedia';
                    }
                    $produk->save();
                }
            }

            $order->update(['status_order' => 'dibatalkan']);
        });

        return back()->with('info', 'Pesanan #' . $order->id_order . ' berhasil dibatalkan.');
    }
}
