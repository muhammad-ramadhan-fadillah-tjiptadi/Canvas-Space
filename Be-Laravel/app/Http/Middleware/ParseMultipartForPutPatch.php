<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\FileBag;

class ParseMultipartForPutPatch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pastikan method adalah PUT atau PATCH dan Content-Type adalah multipart/form-data
        if (
            ($request->isMethod('PUT') || $request->isMethod('PATCH')) &&
            str_contains($request->header('Content-Type', ''), 'multipart/form-data')
        ) {
            try {
                // Fungsi bawaan PHP 8.4+ untuk parsing body request non-POST (PUT/PATCH/DELETE)
                if (function_exists('request_parse_body')) {
                    [$post, $files] = request_parse_body();
                    
                    // Masukkan form data biasa ke dalam request
                    $request->merge($post);
                    
                    // Konversi array file mentah ala $_FILES menjadi objek UploadedFile Symfony/Laravel
                    $fileBag = new FileBag($files);
                    $request->files->replace($fileBag->all());
                }
            } catch (\Throwable $e) {
                // Jika terjadi error saat parsing (misalnya request body tidak valid)
                // Kita abaikan atau log agar request tetap berjalan jika memungkinkan
            }
        }

        return $next($request);
    }
}
