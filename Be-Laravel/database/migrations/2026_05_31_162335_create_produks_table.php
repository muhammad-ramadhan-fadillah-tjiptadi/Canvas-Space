<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
    $table->id();
    $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
    $table->string('nama_produk', 255);
    $table->text('deskripsi')->nullable();
    $table->decimal('harga', 10, 2);
    $table->integer('stok');
    $table->string('gambar', 255)->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
