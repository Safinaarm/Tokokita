<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailOrder extends Model
{
    // Nama tabel detail pesanan
    protected $table = 'detail_order';

    // Primary key
    protected $primaryKey = 'id_detail';

    // Tidak ada timestamps otomatis
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'order_id',
        'product_id',
        'qty',
        'harga',
        'subtotal',
    ];

    /**
     * Relasi ke tabel Order (Header Pesanan)
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id_order');
    }

    /**
     * Relasi ke tabel Produk
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'product_id', 'id_product');
    }
}

// Daftarkan alias agar jika dipanggil Detailorder (huruf kecil) tetap valid
if (!class_exists(Detailorder::class, false)) {
    class_alias(DetailOrder::class, Detailorder::class);
}