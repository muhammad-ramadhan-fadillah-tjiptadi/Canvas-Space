<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProdukController;
use App\Http\Controllers\API\TransaksiController;
use App\Http\Controllers\API\PesananController;

// Route yang bisa diakses tanpa token (Publik)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute Publik (Bisa diakses Pelanggan tanpa Login/Token)
Route::get('/produk', [ProdukController::class, 'index']);
Route::get('/produk/{id}', [ProdukController::class, 'show']);

// Route yang WAJIB menyertakan Token JWT di dalam Header
Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rute Transaksi Berbelanja
    Route::post('/checkout', [PesananController::class, 'checkout']);
    Route::get('/riwayat-pesanan', [PesananController::class, 'riwayat']);

    // Rute Khusus Admin
    Route::middleware('admin')->group(function () {
        Route::get('/admin/pesanan', [PesananController::class, 'semuaPesanan']);
        Route::put('/admin/pesanan/{id}/status', [PesananController::class, 'updateStatus']);

        Route::post('/produk', [ProdukController::class, 'store']);
        Route::match(['put', 'patch', 'post'], '/produk/{id}', [ProdukController::class, 'update'])->middleware('parse.multipart');
        Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);
    });
});
