<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriArtikelResource;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KategoriArtikelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $kategoris = KategoriArtikel::withCount('artikels')
            ->orderBy('nama_kategori')
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Data kategori artikel berhasil diambil',
            'data' => KategoriArtikelResource::collection($kategoris)
        ], Response::HTTP_OK);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $kategori = KategoriArtikel::where('slug', $slug)
            ->withCount('artikels')
            ->first();
        
        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori artikel tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Detail kategori artikel berhasil diambil',
            'data' => new KategoriArtikelResource($kategori)
        ], Response::HTTP_OK);
    }
}