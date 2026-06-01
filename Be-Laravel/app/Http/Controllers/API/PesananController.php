<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PesananController extends Controller
{
    /**
     * Proses Checkout / Buat Pesanan Baru
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_belanja'             => 'required|array|min:1',
            'item_belanja.*.produk_id' => 'required|exists:produk,id',
            'item_belanja.*.jumlah'    => 'required|integer|min:1',
            'metode_bayar'             => 'nullable|string',
            'alamat_pengiriman'        => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Ambil data user yang sedang login dari token JWT
        $user = auth()->guard('api')->user();

        // Menggunakan DB::transaction agar jika salah satu produk error (misal stok habis),
        // semua data yang terlanjur di-insert akan dibatalkan otomatis (Rollback).
        return DB::transaction(function () use ($request, $user) {
            $totalHargaPesanan = 0;
            $DetailDataKeperluan = [];

            // Loop pertama: Validasi stok dan hitung total harga
            foreach ($request->item_belanja as $item) {
                $produk = Produk::find($item['produk_id']);

                // Cek apakah stok mencukupi
                if ($produk->stok < $item['jumlah']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok untuk produk '{$produk->nama_produk}' tidak mencukupi! Sisa stok: {$produk->stok}"
                    ], 400);
                }

                $subtotal = $produk->harga * $item['jumlah'];
                $totalHargaPesanan += $subtotal;

                // Simpan data sementara untuk keperluan insert detail nanti
                $DetailDataKeperluan[] = [
                    'produk_id'    => $produk->id,
                    'jumlah'       => $item['jumlah'],
                    'harga_satuan' => $produk->harga,
                    'subtotal'     => $subtotal,
                    'model_produk' => $produk // Disimpan untuk potong stok nanti
                ];
            }

            $metodeBayar = $request->input('metode_bayar', 'Transfer Bank');
            $alamatPengiriman = $request->input('alamat_pengiriman') ?: ($user->alamat ?: 'Alamat belum diatur');

            // 1. Insert ke tabel `pesanan`
            $pesanan = Pesanan::create([
                'user_id'           => $user->id,
                'total_harga'       => $totalHargaPesanan,
                'status_pesanan'    => 'Pending', // Sesuai kolom database
                'status_bayar'      => 'Belum Lunas', // Sesuai kolom database
                'metode_bayar'      => $metodeBayar, // Sesuai kolom database
                'alamat_pengiriman' => $alamatPengiriman, // Sesuai kolom database
                'tanggal'           => now(),
            ]);

            // 2. Insert ke tabel `detail_pesanan` & Potong Stok Produk
            foreach ($DetailDataKeperluan as $detail) {
                DetailPesanan::create([
                    'pesanan_id'   => $pesanan->id,
                    'produk_id'    => $detail['produk_id'],
                    'kuantitas'    => $detail['jumlah'], // Kolom database: kuantitas
                    'harga_satuan' => $detail['harga_satuan'], // Kolom database: harga_satuan
                    'sub_total'    => $detail['subtotal'], // Kolom database: sub_total
                ]);

                // Potong stok produk secara real-time
                $produk = $detail['model_produk'];
                $produk->stok -= $detail['jumlah'];
                $produk->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil dilakukan!',
                'pesanan' => $pesanan->load('detailPesanan') // Memuat info rinciannya sekaligus
            ], 201);
        });
    }

    /**
     * Melihat Riwayat Pesanan Milik User yang Sedang Login
     */
    public function riwayat()
    {
        $user = auth()->guard('api')->user();
        
        $riwayat = Pesanan::with(['detailPesanan.produk'])
            ->where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $riwayat
        ], 200);
    }
}