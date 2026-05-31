<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['pesanan_id', 'produk_id', 'kuantitas', 'harga_satuan', 'sub_total'])]
class DetailPesanan extends Model
{
    use HasFactory;

    protected $table = 'detail_pesanan';

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id');
    }
}
