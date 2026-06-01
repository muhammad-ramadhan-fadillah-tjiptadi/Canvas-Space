<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            [
                'nama_kategori' => 'Kursi',
                'deskripsi' => 'Koleksi berbagai jenis kursi berkualitas untuk rumah dan kantor Anda.',
            ],
            [
                'nama_kategori' => 'Meja',
                'deskripsi' => 'Koleksi meja makan, meja kerja, dan meja hias minimalis.',
            ],
            [
                'nama_kategori' => 'Sofa',
                'deskripsi' => 'Sofa nyaman dan elegan untuk ruang tamu Anda.',
            ],
            [
                'nama_kategori' => 'Lemari',
                'deskripsi' => 'Lemari pakaian, lemari buku, dan kabinet penyimpanan modern.',
            ],
            [
                'nama_kategori' => 'Tempat Tidur',
                'deskripsi' => 'Rangka tempat tidur, kasur, dan perlengkapan tidur berkualitas.',
            ],
        ];

        foreach ($kategori as $item) {
            Kategori::create($item);
        }
    }
}
