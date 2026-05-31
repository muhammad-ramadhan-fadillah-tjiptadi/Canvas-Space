<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Produk;

class TransaksiController extends Controller
{
    public function checkout(Request $request)
    {
        // Memulai Database Transaction
        DB::beginTransaction();

        try {
            // 1. Catat ke tabel pesanan
            $pesanan = Pesanan::create([
                'user_id' => auth()->user()->id,
                'tanggal' => now(),
                'total_harga' => $request->total_harga,
                'status_pesanan' => 'Pending',
                'metode_bayar' => $request->metode_bayar,
                'status_bayar' => 'Belum Lunas',
                'alamat_pengiriman' => $request->alamat_pengiriman
            ]);

            // 2. Loop item produk dari Keranjang Belanja React
            foreach ($request->items as $item) {
                $produk = Produk::lockForUpdate()->find($item['produk_id']);

                // Validasi ketersediaan stok produk furnitur
                if ($produk->stok < $item['kuantitas']) {
                    throw new \Exception("Stok untuk produk " . $produk->nama_produk . " tidak mencukupi.");
                }

                // Catat detail transaksi
                DetailPesanan::create([
                    'pesanan_id' => $pesanan->id,
                    'produk_id' => $produk->id,
                    'kuantitas' => $item['kuantitas'],
                    'harga_satuan' => $produk->harga, // Harga dikunci saat transaksi
                    'sub_total' => $produk->harga * $item['kuantitas']
                ]);

                // 3. Potong stok otomatis
                $produk->stok -= $item['kuantitas'];
                $produk->save();
            }

            // Jika semua operasi sukses, simpan permanen ke database
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pesanan berhasil diproses!'], 201);

        } catch (\Exception $e) {
            // Jika ada satu saja operasi yang gagal, batalkan seluruhnya
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}