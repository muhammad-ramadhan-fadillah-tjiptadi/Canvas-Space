<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['user_id', 'tanggal', 'total_harga', 'status_pesanan', 'metode_bayar', 'status_bayar', 'alamat_pengiriman'])]
class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }
}
