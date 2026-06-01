<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    /**
     * Menampilkan semua data produk (Bisa diakses Publik/Pelanggan)
     */
    public function index()
    {
        $produk = Produk::with('kategori')->get();
        
        return response()->json([
            'success' => true,
            'data' => $produk
        ], 200);
    }

    /**
     * Menyimpan produk baru + Upload Gambar (Khusus Admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori_id'  => 'required|exists:kategori,id',
            'nama_produk'  => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'harga'        => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'gambar'       => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', // Batas 2MB
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Logika Upload Gambar ke folder storage/app/public/produk
        $gambarPath = $request->file('gambar')->store('produk', 'public');

        $produk = Produk::create([
            'kategori_id' => $request->kategori_id,
            'nama_produk' => $request->nama_produk,
            'deskripsi'   => $request->deskripsi,
            'harga'       => $request->harga,
            'stok'        => $request->stok,
            'gambar'      => Storage::url($gambarPath), // Menghasilkan URL publik gambar
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk furnitur berhasil ditambahkan!',
            'data'    => $produk
        ], 201);
    }

    /**
     * Menampilkan detail satu produk
     */
    public function show($id)
    {
        $produk = Produk::with('kategori')->find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan!'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $produk
        ], 200);
    }

    /**
     * Mengubah data produk & memperbarui gambar lama (Khusus Admin)
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan!'], 404);
        }

        $validator = Validator::make($request->all(), [
            'kategori_id'  => 'required|exists:kategori,id',
            'nama_produk'  => 'required|string|max:255',
            'deskripsi'    => 'nullable|string',
            'harga'        => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'gambar'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update data teks biasa
        $produk->kategori_id = $request->kategori_id;
        $produk->nama_produk = $request->nama_produk;
        $produk->deskripsi   = $request->deskripsi;
        $produk->harga       = $request->harga;
        $produk->stok        = $request->stok;

        // Jika ada file gambar baru yang diunggah
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama dari storage agar tidak memenuhi server
            $oldPath = str_replace('/storage/', '', $produk->gambar);
            Storage::disk('public')->delete($oldPath);

            // Simpan gambar baru
            $gambarPath = $request->file('gambar')->store('produk', 'public');
            $produk->gambar = Storage::url($gambarPath);
        }

        $produk->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk furnitur berhasil diperbarui!',
            'data'    => $produk
        ], 200);
    }

    /**
     * Menghapus produk beserta file gambarnya (Khusus Admin)
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan!'], 404);
        }

        // Hapus file gambarnya dari storage sebelum record di database dihapus
        $path = str_replace('/storage/', '', $produk->gambar);
        Storage::disk('public')->delete($path);

        $produk->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari sistem!'
        ], 200);
    }
}