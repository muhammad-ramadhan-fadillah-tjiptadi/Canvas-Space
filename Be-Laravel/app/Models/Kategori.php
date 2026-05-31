<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['nama_kategori', 'deskripsi'])]
class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    public function produks()
    {
        return $this->hasMany(Produk::class, 'kategori_id');
    }
}
