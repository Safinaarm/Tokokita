<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Nama tabel order
    protected $table = 'order';

    // Primary key
    protected $primaryKey = 'id_order';

    // Tidak menggunakan timestamps otomatis
    public $timestamps = false;

    // Kolom yang dapat diisi
    protected $fillable = [
        'pembeli_id',
        'tgl_order',
        'total_harga',
        'status_order',
        'alamat_pengiriman',
        'bukti_bayar',
    ];

    /**
     * Relasi ke pembeli (User)
     */
    public function pembeli()
    {
        return $this->belongsTo(User::class, 'pembeli_id', 'id_user');
    }

    /**
     * Relasi ke rincian pesanan (DetailOrder)
     */
    public function details()
    {
        return $this->hasMany(DetailOrder::class, 'order_id', 'id_order');
    }

    /**
     * Alias relasi detail pesanan
     */
    public function detail()
    {
        return $this->details();
    }

    /**
     * Label status order dalam bahasa Indonesia
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_order) {
            'menunggu_bayar'       => 'Menunggu Pembayaran',
            'menunggu_konfirmasi'  => 'Menunggu Konfirmasi',
            'diproses'             => 'Sedang Diproses',
            'dikirim'              => 'Sedang Dikirim',
            'selesai'              => 'Selesai',
            'dibatalkan'           => 'Dibatalkan',
            default                => ucfirst($this->status_order),
        };
    }

    /**
     * Class CSS badge untuk status pesanan
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status_order) {
            'menunggu_bayar'       => 'bg-amber-100 text-amber-800 border-amber-300',
            'menunggu_konfirmasi'  => 'bg-blue-100 text-blue-800 border-blue-300',
            'diproses'             => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'dikirim'              => 'bg-purple-100 text-purple-800 border-purple-300',
            'selesai'              => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'dibatalkan'           => 'bg-rose-100 text-rose-800 border-rose-300',
            default                => 'bg-gray-100 text-gray-800 border-gray-300',
        };
    }
}