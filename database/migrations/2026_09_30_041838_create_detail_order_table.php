<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel detail_order (isi produk tiap order).
     */
    public function up(): void
    {
        Schema::create('detail_order', function (Blueprint $table) {
            $table->increments('id_detail');             // primary key
            $table->unsignedInteger('order_id');         // FK ke order.id_order
            $table->unsignedInteger('product_id');       // FK ke produk.id_product
            $table->integer('qty')->default(1);
            $table->decimal('harga', 12, 2);             // harga satuan saat order
            $table->decimal('subtotal', 12, 2);

            // Detail ikut terhapus kalau ordernya dihapus
            $table->foreign('order_id')
                  ->references('id_order')->on('order')
                  ->cascadeOnDelete()
                  ->cascadeOnUpdate();

            // Produk yang sudah pernah dipesan tidak boleh dihapus
            $table->foreign('product_id')
                  ->references('id_product')->on('produk')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
        });
    }

    /**
     * Menghapus tabel detail_order.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_order');
    }
};