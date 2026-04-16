<?php

use App\Http\Controllers\Api\ArtikelController;
use App\Http\Controllers\Api\KategoriArtikelController;
use App\Http\Controllers\Api\PengaduanController;
use App\Http\Controllers\Api\TernakController;
use App\Http\Controllers\Api\FatteningController;  
use App\Http\Controllers\Api\PerkawinanController;  
use Illuminate\Support\Facades\Route;

// Landing Page API - Tanpa Auth
Route::prefix('v1')->group(function () {
    
    // Ternak - Untuk menampilkan gallery/koleksi ternak
    Route::prefix('ternak')->group(function () {
        Route::get('/', [TernakController::class, 'index']);
        Route::get('/{slug}', [TernakController::class, 'getBySlug']);
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
// Fattening routes
Route::prefix('fattening')->group(function () {
    Route::get('/', [FatteningController::class, 'index']);
    Route::post('/', [FatteningController::class, 'store']);
    Route::get('/statistics', [FatteningController::class, 'statistics']);
    Route::get('/ternak/{slug}', [FatteningController::class, 'getByTernakSlug']);
    Route::get('/{id}', [FatteningController::class, 'show']);
    Route::put('/{id}', [FatteningController::class, 'update']);
    Route::patch('/{id}/bobot', [FatteningController::class, 'updateBobot']);
    Route::delete('/{id}', [FatteningController::class, 'destroy']);
});

// Perkawinan/Breeding routes
Route::prefix('perkawinan')->group(function () {
    Route::get('/', [PerkawinanController::class, 'index']);
    Route::post('/', [PerkawinanController::class, 'store']);
    Route::get('/statistics', [PerkawinanController::class, 'statistics']);
    Route::get('/active-pregnancies', [PerkawinanController::class, 'getActivePregnancies']);
    Route::get('/betina/{slug}', [PerkawinanController::class, 'getByBetinaSlug']);
    Route::get('/{id}', [PerkawinanController::class, 'show']);
    Route::put('/{id}', [PerkawinanController::class, 'update']);
    Route::patch('/{id}/pregnancy-status', [PerkawinanController::class, 'updatePregnancyStatus']);
    Route::delete('/{id}', [PerkawinanController::class, 'destroy']);
});
