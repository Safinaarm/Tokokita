<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel produk.
     */
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->increments('id_product');            // primary key
            $table->unsignedInteger('penjual_id');       // FK ke users.id_user
            $table->string('nama_product', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 12, 2);
            $table->integer('stok')->default(0);
            $table->string('kategori', 50);
            $table->string('gambar_product', 255)->nullable();
            $table->enum('status_product', ['tersedia', 'habis'])->default('tersedia');

            // Relasi ke tabel users (penjual pemilik produk)
            $table->foreign('penjual_id')
                  ->references('id_user')->on('users')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel produk.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};