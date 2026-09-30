<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    // Nama tabel produk
    protected $table = 'produk';

    // Primary key
    protected $primaryKey = 'id_product';

    // Tidak menggunakan timestamps otomatis
    public $timestamps = false;

    // Kolom yang dapat diisi secara mass-assignment
    protected $fillable = [
        'penjual_id',
        'nama_product',
        'deskripsi',
        'harga',
        'stok',
        'kategori',
        'gambar_product',
        'status_product',
    ];

    /**
     * Relasi: Satu produk dimiliki oleh satu user (penjual)
     */
    public function penjual()
    {
        return $this->belongsTo(User::class, 'penjual_id', 'id_user');
    }

    /**
     * Relasi ke rincian transaksi
     */
    public function detailOrders()
    {
        return $this->hasMany(DetailOrder::class, 'product_id', 'id_product');
    }

    /**
     * Helper URL gambar produk (mendukung URL eksternal maupun file lokal)
     */
    public function getGambarUrlAttribute(): string
    {
        if (!empty($this->gambar_product)) {
            // Jika sudah berupa URL lengkap
            if (str_starts_with($this->gambar_product, 'http://') || str_starts_with($this->gambar_product, 'https://')) {
                return $this->gambar_product;
            }

            // Jika tersimpan di storage lokal (storage/app/public)
            if (file_exists(public_path('storage/' . $this->gambar_product))) {
                return asset('storage/' . $this->gambar_product);
            }

            if (file_exists(public_path('storage/produk/' . $this->gambar_product))) {
                return asset('storage/produk/' . $this->gambar_product);
            }

            if (file_exists(public_path($this->gambar_product))) {
                return asset($this->gambar_product);
            }
        }

        // Gambar default placeholder jika tidak ada foto
        return 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&auto=format&fit=crop&q=60';
    }

    /**
     * Helper format harga rupiah
     */
    public function getHargaRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }
}