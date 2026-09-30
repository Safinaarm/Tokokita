<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller Manajemen Order Penjual
 * Menangani validasi order, verifikasi bukti transfer, konfirmasi (terima/tolak),
 * serta pembaruan status pengiriman pesanan
 */
class PenjualOrderController extends Controller
{
    /**
     * Menampilkan daftar semua order masuk yang memuat produk milik penjual
     */
    public function index(Request $request)
    {
        $penjualId = Auth::user()->id_user;
        $myProductIds = Produk::where('penjual_id', $penjualId)->pluck('id_product');

        $query = Order::whereHas('details', function ($q) use ($myProductIds) {
            $q->whereIn('product_id', $myProductIds);
        })->with(['pembeli', 'details' => function ($q) use ($myProductIds) {
            $q->whereIn('product_id', $myProductIds)->with('produk');
        }]);

        // Filter status order jika dipilih
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status_order', $request->status);
        }

        // Pencarian nomor order atau nama pembeli
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('id_order', 'like', "%{$keyword}%")
                  ->orWhereHas('pembeli', function ($u) use ($keyword) {
                      $u->where('nama_user', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                  });
            });
        }

        $orders = $query->orderByDesc('id_order')->paginate(10)->withQueryString();

        return view('penjual.order.index', compact('orders'));
    }

    /**
     * Menampilkan detail order tertentu (termasuk verifikasi bukti bayar)
     */
    public function show($id)
    {
        $penjualId = Auth::user()->id_user;
        $myProductIds = Produk::where('penjual_id', $penjualId)->pluck('id_product');

        $order = Order::whereHas('details', function ($q) use ($myProductIds) {
            $q->whereIn('product_id', $myProductIds);
        })->with(['pembeli', 'details.produk'])->findOrFail($id);

        return view('penjual.order.show', compact('order'));
    }

    /**
     * Memperbarui status pesanan (Konfirmasi terima/tolak, kirim, selesaikan)
     */
    public function updateStatus(Request $request, $id)
    {
        $penjualId = Auth::user()->id_user;
        $myProductIds = Produk::where('penjual_id', $penjualId)->pluck('id_product');

        $order = Order::whereHas('details', function ($q) use ($myProductIds) {
            $q->whereIn('product_id', $myProductIds);
        })->with('details')->findOrFail($id);

        $request->validate([
            'status_order' => 'required|in:menunggu_bayar,menunggu_konfirmasi,diproses,dikirim,selesai,dibatalkan',
        ]);

        $statusLama = $order->status_order;
        $statusBaru = $request->status_order;

        DB::transaction(function () use ($order, $statusLama, $statusBaru) {
            // Jika pesanan ditolak / dibatalkan, kembalikan stok produk jika belum dibatalkan sebelumnya
            if ($statusBaru === 'dibatalkan' && $statusLama !== 'dibatalkan') {
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
            }

            $order->update(['status_order' => $statusBaru]);
        });

        $pesan = match ($statusBaru) {
            'diproses'   => 'Pesanan berhasil dikonfirmasi dan sedang diproses!',
            'dikirim'    => 'Pesanan telah diubah statusnya menjadi sedang dikirim!',
            'selesai'    => 'Pesanan telah berhasil diselesaikan.',
            'dibatalkan' => 'Pesanan telah dibatalkan dan stok produk telah dikembalikan.',
            default      => 'Status pesanan berhasil diperbarui.',
        };

        return back()->with('success', $pesan);
    }
}
