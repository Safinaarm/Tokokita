<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel order.
     */
    public function up(): void
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id_order');              // primary key
            $table->unsignedInteger('pembeli_id');       // FK ke users.id_user
            $table->dateTime('tgl_order')->useCurrent();
            $table->decimal('total_harga', 12, 2);
            $table->enum('status_order', [
                'menunggu_bayar',
                'menunggu_konfirmasi',
                'diproses',
                'dikirim',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_bayar');
            $table->text('alamat_pengiriman');
            $table->string('bukti_bayar', 255)->nullable();

            // Relasi ke tabel users (pembeli yang membuat order)
            $table->foreign('pembeli_id')
                  ->references('id_user')->on('users')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel order.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};