<?php

namespace Database\Seeders;

use App\Models\DetailOrder;
use App\Models\Order;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk mengisi data awal aplikasi MarketPlace Sederhana
     * Sesuai ketentuan UJIKOM:
     * - Minimal 1 Penjual
     * - Minimal 1 Pembeli
     * - Minimal 5 Produk
     */
    public function run(): void
    {
        // 1. Akun Penjual
        $penjual = User::updateOrCreate(
            ['email' => 'penjual@ujikom.test'],
            [
                'nama_user'   => 'Toko Berkah Mandiri',
                'password'    => Hash::make('password123'),
                'role'        => 'penjual',
                'tgl_daftar'  => now(),
                'status_user' => 'aktif',
            ]
        );

        // 2. Akun Pembeli
        $pembeli = User::updateOrCreate(
            ['email' => 'pembeli@ujikom.test'],
            [
                'nama_user'   => 'Siti Pembeli',
                'password'    => Hash::make('password123'),
                'role'        => 'pembeli',
                'tgl_daftar'  => now(),
                'status_user' => 'aktif',
            ]
        );

        // 3. 5 Data Produk Sesuai Ketentuan Soal (Langkah Kerja No. 4)
        $daftarProduk = [
            [
                'penjual_id'     => $penjual->id_user,
                'nama_product'   => 'Kaos Polos Katun Combed 30s',
                'deskripsi'      => 'Kaos polos katun combed 30s premium, adem, menyerap keringat, dan cocok untuk kegiatan santai sehari-hari.',
                'harga'          => 55000,
                'stok'           => 40,
                'kategori'       => 'Pakaian',
                'gambar_product' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
            [
                'penjual_id'     => $penjual->id_user,
                'nama_product'   => 'Sepatu Sneakers Pria Casual',
                'deskripsi'      => 'Sepatu sneakers dengan desain modern, sol karet empuk anti licin, nyaman untuk aktivitas jalan-jalan dan kuliah.',
                'harga'          => 250000,
                'stok'           => 15,
                'kategori'       => 'Sepatu',
                'gambar_product' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
            
            [
                'penjual_id'     => $penjual->id_user,
                'nama_product'   => 'Headset Bluetooth Wireless Bass',
                'deskripsi'      => 'Headphone nirkabel Bluetooth 5.3 dengan suara bass bertenaga, peredam bising, dan baterai tahan hingga 20 jam.',
                'harga'          => 120000,
                'stok'           => 25,
                'kategori'       => 'Elektronik',
                'gambar_product' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'tersedia',
            ],
            [
                'penjual_id'     => $penjual->id_user,
                'nama_product'   => 'Topi Baseball Katun Klasik',
                'deskripsi'      => 'Topi baseball model retro klasik, tali belakang bisa disetel, melindungi dari terik matahari dengan gaya elegan.',
                'harga'          => 35000,
                'stok'           => 0,
                'kategori'       => 'Aksesoris',
                'gambar_product' => 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=600&auto=format&fit=crop&q=80',
                'status_product' => 'habis',
            ],
        ];

        foreach ($daftarProduk as $p) {
            Produk::updateOrCreate(
                [
                    'penjual_id'   => $p['penjual_id'],
                    'nama_product' => $p['nama_product'],
                ],
                $p
            );
        }
    }
}
