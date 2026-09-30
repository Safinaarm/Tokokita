<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\PembeliOrderController;
use App\Http\Controllers\PenjualDashboardController;
use App\Http\Controllers\PenjualOrderController;
use App\Http\Controllers\PenjualProdukController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - MarketPlace Sederhana (UJIKOM Web Developer)
|--------------------------------------------------------------------------
| Seluruh rute aplikasi didefinisikan di sini dengan pengelompokan:
| 1. Rute Publik (Katalog & Detail Produk)
| 2. Rute Autentikasi Pengguna (Login, Register, Logout)
| 3. Rute Keranjang Belanja (Cart)
| 4. Rute Khusus Pembeli (Checkout, Order, Bukti Bayar)
| 5. Rute Khusus Penjual/Admin (Dashboard, Produk CRUD, Validasi Order)
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. RUTE PUBLIK (Landing Page & Katalog)
// ==========================================
Route::get('/', [KatalogController::class, 'index'])->name('landing');
Route::get('/produk/{id}', [KatalogController::class, 'show'])->name('produk.detail');

// ==========================================
// 2. RUTE AUTENTIKASI PENGGUNA
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ==========================================
// 3. RUTE KERANJANG BELANJA (CART)
// ==========================================
Route::prefix('keranjang')->name('keranjang.')->group(function () {
    Route::get('/', [KeranjangController::class, 'index'])->name('index');
    Route::post('/tambah/{id}', [KeranjangController::class, 'add'])->name('add');
    Route::post('/update/{id}', [KeranjangController::class, 'update'])->name('update');
    Route::post('/hapus/{id}', [KeranjangController::class, 'remove'])->name('remove');
    Route::post('/kosongkan', [KeranjangController::class, 'clear'])->name('clear');
});

// ==========================================
// 4. RUTE KHUSUS PEMBELI
// ==========================================
Route::middleware(['auth', 'role:pembeli'])->prefix('pembeli')->name('pembeli.')->group(function () {
    // Dashboard pembeli (menampilkan status akun & ringkasan pesanan)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $recentOrders = \App\Models\Order::where('pembeli_id', $user->id_user)
            ->with('details.produk')
            ->orderByDesc('id_order')
            ->limit(5)
            ->get();
        return view('pembeli.dashboard', compact('user', 'recentOrders'));
    })->name('dashboard');

    // Checkout & Pembuatan Pesanan
    Route::get('/checkout', [PembeliOrderController::class, 'showCheckout'])->name('checkout');
    Route::post('/checkout', [PembeliOrderController::class, 'prosesCheckout'])->name('checkout.proses');

    // Daftar Pesanan & Detail
    Route::get('/pesanan', [PembeliOrderController::class, 'daftarPesanan'])->name('pesanan.index');
    Route::get('/pesanan/{id}', [PembeliOrderController::class, 'detailPesanan'])->name('pesanan.detail');

    // Upload Bukti Pembayaran
    Route::post('/pesanan/{id}/bukti', [PembeliOrderController::class, 'uploadBuktiBayar'])->name('pesanan.bukti');

    // Konfirmasi Pesanan Selesai
    Route::post('/pesanan/{id}/selesai', [PembeliOrderController::class, 'konfirmasiSelesai'])->name('pesanan.selesai');

    // Batalkan Pesanan (jika masih menunggu_bayar)
    Route::post('/pesanan/{id}/batal', [PembeliOrderController::class, 'batalkanPesanan'])->name('pesanan.batal');
});

// ==========================================
// 5. RUTE KHUSUS PENJUAL (ADMIN TOKO)
// ==========================================
Route::middleware(['auth', 'role:penjual'])->prefix('penjual')->name('penjual.')->group(function () {
    // Dashboard Penjual (Statistik & Metrik)
    Route::get('/dashboard', [PenjualDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Produk (Katalog Produk Penjual)
    Route::prefix('produk')->name('produk.')->group(function () {
        Route::get('/', [PenjualProdukController::class, 'index'])->name('index');
        Route::get('/tambah', [PenjualProdukController::class, 'create'])->name('create');
        Route::post('/simpan', [PenjualProdukController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PenjualProdukController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [PenjualProdukController::class, 'update'])->name('update');
        Route::post('/{id}/toggle-status', [PenjualProdukController::class, 'toggleStatus'])->name('toggle');
        Route::delete('/{id}/hapus', [PenjualProdukController::class, 'destroy'])->name('destroy');
    });

    // Manajemen Order (Validasi, Verifikasi Bukti, & Konfirmasi Status)
    Route::prefix('order')->name('order.')->group(function () {
        Route::get('/', [PenjualOrderController::class, 'index'])->name('index');
        Route::get('/{id}', [PenjualOrderController::class, 'show'])->name('show');
        Route::post('/{id}/status', [PenjualOrderController::class, 'updateStatus'])->name('status');
    });
});