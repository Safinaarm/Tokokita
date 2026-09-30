<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Nama tabel di database
    protected $table = 'users';

    // Primary key custom sesuai skema UJIKOM
    protected $primaryKey = 'id_user';

    // Tabel tidak menggunakan created_at dan updated_at standar
    public $timestamps = false;

    // Kolom yang dapat diisi secara mass-assignment
    protected $fillable = [
        'nama_user',
        'email',
        'password',
        'role',
        'tgl_daftar',
        'foto_profil',
        'status_user',
    ];

    // Kolom yang disembunyikan saat serialisasi JSON
    protected $hidden = [
        'password',
    ];

    // Nonaktifkan remember_token karena kolom tidak ada di tabel users
    public function getRememberTokenName()
    {
        return '';
    }

    /**
     * Relasi: Satu penjual dapat memiliki banyak produk
     */
    public function produk()
    {
        return $this->hasMany(Produk::class, 'penjual_id', 'id_user');
    }

    /**
     * Relasi: Satu pembeli dapat memiliki banyak order
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'pembeli_id', 'id_user');
    }

    /**
     * Helper untuk memeriksa apakah user adalah penjual
     */
    public function isPenjual(): bool
    {
        return $this->role === 'penjual';
    }

    /**
     * Helper untuk memeriksa apakah user adalah pembeli
     */
    public function isPembeli(): bool
    {
        return $this->role === 'pembeli';
    }
}