<?php

namespace Tests\Feature;

use App\Models\DetailOrder;
use App\Models\Order;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Pengujian Alur Sistem Marketplace Sederhana Sesuai Soal UJIKOM
 */
class MarketplaceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'ujikom',
        ]);
    }

    /**
     * Uji Halaman Landing Page & Katalog Produk
     */
    public function test_landing_page_can_be_rendered()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('TokoKita');
        $response->assertSee('Katalog Produk');
    }

    /**
     * Uji Halaman Login & Registrasi
     */
    public function test_login_and_register_pages_are_accessible()
    {
        $responseLogin = $this->get('/login');
        $responseLogin->assertStatus(200);
        $responseLogin->assertSee('Selamat Datang');

        $responseRegister = $this->get('/register');
        $responseRegister->assertStatus(200);
        $responseRegister->assertSee('Formulir Pendaftaran');
    }

    /**
     * Uji Registrasi Akun Pembeli Baru
     */
    public function test_pembeli_can_register()
    {
        $email = 'pembeli_test_' . time() . '@test.com';
        $response = $this->post('/register', [
            'nama_user'             => 'Pembeli Baru Test',
            'email'                 => $email,
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'pembeli',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'role'  => 'pembeli',
        ]);
    }

    /**
     * Uji Login Penjual & Akses Dashboard
     */
    public function test_penjual_can_login_and_access_dashboard()
    {
        $penjual = User::where('role', 'penjual')->first();
        $this->assertNotNull($penjual);

        $response = $this->post('/login', [
            'email'    => $penjual->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('penjual.dashboard'));

        $this->actingAs($penjual);
        $dashboardResponse = $this->get(route('penjual.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Panel Penjual');
    }

    /**
     * Uji Alur Keranjang Belanja Pembeli
     */
    public function test_cart_workflow()
    {
        $produk = Produk::where('status_product', 'tersedia')->where('stok', '>', 0)->first();
        $this->assertNotNull($produk);

        // Tambah ke keranjang
        $response = $this->post(route('keranjang.add', $produk->id_product), ['qty' => 1]);
        $response->assertRedirect(route('keranjang.index'));

        // Cek halaman keranjang
        $cartPage = $this->get(route('keranjang.index'));
        $cartPage->assertStatus(200);
        $cartPage->assertSee($produk->nama_product);
    }

    /**
     * Uji Penjual Menambahkan Produk Baru
     */
    public function test_penjual_can_create_product()
    {
        $penjual = User::where('role', 'penjual')->first();
        $this->actingAs($penjual);

        $response = $this->post(route('penjual.produk.store'), [
            'nama_product'   => 'Jaket Fleece ' . time(),
            'kategori'       => 'Pakaian',
            'harga'          => 175000,
            'stok'           => 12,
            'deskripsi'      => 'Bahan katun fleece tebal dan lembut.',
            'status_product' => 'tersedia',
        ]);

        $response->assertRedirect(route('penjual.produk.index'));
    }

    /**
     * Uji End-to-End Transaksi: Checkout -> Upload Bukti Bayar -> Penjual Konfirmasi
     */
    public function test_full_transaction_flow()
    {
        Storage::fake('public');

        $pembeli = User::where('role', 'pembeli')->first();
        $penjual = User::where('role', 'penjual')->first();
        $produk = Produk::where('status_product', 'tersedia')->where('stok', '>=', 2)->first();

        $this->assertNotNull($pembeli);
        $this->assertNotNull($penjual);
        $this->assertNotNull($produk);

        // 1. Pembeli masukkan produk ke keranjang
        $this->actingAs($pembeli);
        $this->post(route('keranjang.add', $produk->id_product), ['qty' => 2]);

        // 2. Pembeli checkout
        $checkoutResponse = $this->post(route('pembeli.checkout.proses'), [
            'nama_penerima'  => 'Siti Pembeli Test',
            'no_telepon'     => '081234567890',
            'alamat_lengkap' => 'Jl. Mawar No. 12, Kota Surabaya',
            'catatan'        => 'Kirim sore hari',
        ]);

        // Pastikan order terbuat di DB
        $order = Order::where('pembeli_id', $pembeli->id_user)->latest('id_order')->first();
        $this->assertNotNull($order);
        $this->assertEquals('menunggu_bayar', $order->status_order);
        $checkoutResponse->assertRedirect(route('pembeli.pesanan.detail', $order->id_order));

        // 3. Pembeli upload bukti bayar
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');
        $uploadResponse = $this->post(route('pembeli.pesanan.bukti', $order->id_order), [
            'bukti_bayar' => $file,
        ]);
        $uploadResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('menunggu_konfirmasi', $order->status_order);
        $this->assertNotNull($order->bukti_bayar);

        // 4. Penjual melihat order dan mengonfirmasi (status jadi 'diproses')
        $this->actingAs($penjual);
        $orderIndex = $this->get(route('penjual.order.index'));
        $orderIndex->assertStatus(200);

        $confirmResponse = $this->post(route('penjual.order.status', $order->id_order), [
            'status_order' => 'diproses',
        ]);
        $confirmResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('diproses', $order->status_order);

        // 5. Penjual kirim order (status jadi 'dikirim')
        $this->post(route('penjual.order.status', $order->id_order), [
            'status_order' => 'dikirim',
        ]);
        $order->refresh();
        $this->assertEquals('dikirim', $order->status_order);

        // 6. Pembeli konfirmasi pesanan diterima (status jadi 'selesai')
        $this->actingAs($pembeli);
        $selesaiResponse = $this->post(route('pembeli.pesanan.selesai', $order->id_order));
        $selesaiResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('selesai', $order->status_order);
    }
}
