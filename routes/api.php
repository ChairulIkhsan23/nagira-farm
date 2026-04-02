<?php

use App\Http\Controllers\Api\ArtikelController;
use App\Http\Controllers\Api\KategoriArtikelController;
use App\Http\Controllers\Api\PengaduanController;
use App\Http\Controllers\Api\TernakController;
use Illuminate\Support\Facades\Route;

// Landing Page API - Tanpa Auth
Route::prefix('v1')->group(function () {
    
    // Ternak - Untuk menampilkan gallery/koleksi ternak
    Route::prefix('ternak')->group(function () {
        Route::get('/', [TernakController::class, 'index']);
        Route::get('/{slug}', [TernakController::class, 'show']);
        Route::get('/jenis/{jenis}', [TernakController::class, 'byJenis']);
    });
    
    // Artikel - Untuk konten blog/berita
    Route::prefix('artikel')->group(function () {
        Route::get('/', [ArtikelController::class, 'index']);
        Route::get('/latest', [ArtikelController::class, 'latest']);
        Route::get('/featured', [ArtikelController::class, 'featured']);
        Route::get('/{slug}', [ArtikelController::class, 'show']);
        Route::get('/kategori/{kategoriSlug}', [ArtikelController::class, 'byKategori']);
    });
    
    // Kategori Artikel
    Route::get('/kategori-artikel', [KategoriArtikelController::class, 'index']);
    Route::get('/kategori-artikel/{slug}', [KategoriArtikelController::class, 'show']);
    
    // Pengaduan - Untuk form kontak/feedback (POST tanpa auth)
    Route::post('/pengaduan', [PengaduanController::class, 'store']);
});
