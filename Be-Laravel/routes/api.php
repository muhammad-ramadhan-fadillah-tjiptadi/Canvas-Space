<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

// Route yang bisa diakses tanpa token (Publik)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route yang WAJIB menyertakan Token JWT di dalam Header
Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Route khusus untuk User dengan Role 'Admin' (Wajib Sekolah)
    Route::middleware('admin')->group(function () {
        // CRUD Produk akan diletakkan di sini oleh Anda di tahap berikutnya
        // Contoh:
        // Route::post('/produk', [ProdukController::class, 'store']);
        // Route::put('/produk/{id}', [ProdukController::class, 'update']);
        // Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);
    });
});