<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['kategori_id', 'nama_produk', 'deskripsi', 'harga', 'stok', 'gambar'])]
class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class, 'produk_id');
    }
}
