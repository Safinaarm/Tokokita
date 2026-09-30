<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;

/**
 * Controller Dashboard Penjual (Admin Toko)
 * Menyajikan rangkuman metrik penjualan, katalog produk, dan pesanan terbaru
 */
class PenjualDashboardController extends Controller
{
    /**
     * Menampilkan dashboard utama penjual
     */
    public function index()
    {
        $penjualId = Auth::user()->id_user;

        // Metrik Produk
        $totalProduk = Produk::where('penjual_id', $penjualId)->count();
        $produkHabis = Produk::where('penjual_id', $penjualId)
            ->where(function ($q) {
                $q->where('status_product', 'habis')->orWhere('stok', '<=', 0);
            })->count();

        // Ambil ID semua produk milik penjual ini
        $myProductIds = Produk::where('penjual_id', $penjualId)->pluck('id_product');

        // Query Order yang memuat minimal 1 produk milik penjual ini
        $orderQuery = Order::whereHas('details', function ($q) use ($myProductIds) {
            $q->whereIn('product_id', $myProductIds);
        });

        $totalOrder = (clone $orderQuery)->count();
        $orderPerluKonfirmasi = (clone $orderQuery)->where('status_order', 'menunggu_konfirmasi')->count();
        $orderDiproses = (clone $orderQuery)->where('status_order', 'diproses')->count();

        // Total pendapatan dari order yang telah selesai
        $totalPendapatan = (clone $orderQuery)->where('status_order', 'selesai')->sum('total_harga');

        // 5 Order terbaru
        $recentOrders = (clone $orderQuery)
            ->with(['pembeli', 'details.produk'])
            ->orderByDesc('id_order')
            ->limit(5)
            ->get();

        return view('penjual.dashboard', compact(
            'totalProduk',
            'produkHabis',
            'totalOrder',
            'orderPerluKonfirmasi',
            'orderDiproses',
            'totalPendapatan',
            'recentOrders'
        ));
    }
}
