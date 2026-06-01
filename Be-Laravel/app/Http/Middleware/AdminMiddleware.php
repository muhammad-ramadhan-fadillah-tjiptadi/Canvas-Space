<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan user sudah login (terautentikasi) dan memiliki role 'Admin'
        if (auth()->check() && auth()->user()->role === 'Admin') {
            return $next($request);
        }

        // Jika bukan Admin, kembalikan respon error 403 (Forbidden)
        return response()->json([
            'success' => false,
            'message' => 'Akses ditolak! Hanya Admin yang diperbolehkan mengakses fitur ini.'
        ], 403);
    }
}
