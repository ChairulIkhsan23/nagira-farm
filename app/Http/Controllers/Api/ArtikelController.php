<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArtikelResource;
use App\Models\Artikel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ArtikelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Artikel::query()
            ->where('status', 'published')
            ->where('tanggal_publish', '<=', now())
            ->with('kategori');
        
        // Filter by kategori
        if ($request->has('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }
        
        // Search by judul
        if ($request->has('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'tanggal_publish');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $perPage = $request->get('per_page', 12);
        $artikels = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'message' => 'Data artikel berhasil diambil',
            'data' => [
                'data' => ArtikelResource::collection($artikels),
                'meta' => [
                    'current_page' => $artikels->currentPage(),
                    'per_page' => $artikels->perPage(),
                    'total' => $artikels->total(),
                    'last_page' => $artikels->lastPage(),
                ]
            ]
        ], Response::HTTP_OK);
    }
    
    /**
     * Get latest articles for landing page.
     */
    public function latest(Request $request)
    {
        $limit = $request->get('limit', 5);
        
        $artikels = Artikel::where('status', 'published')
            ->where('tanggal_publish', '<=', now())
            ->with('kategori')
            ->orderBy('tanggal_publish', 'desc')
            ->limit($limit)
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Artikel terbaru berhasil diambil',
            'data' => ArtikelResource::collection($artikels)
        ], Response::HTTP_OK);
    }
    
    /**
     * Get featured articles for landing page.
     */
    public function featured(Request $request)
    {
        $limit = $request->get('limit', 3);
        
        $artikels = Artikel::where('status', 'published')
            ->where('tanggal_publish', '<=', now())
            ->where('is_featured', true)
            ->with('kategori')
            ->orderBy('tanggal_publish', 'desc')
            ->limit($limit)
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Artikel unggulan berhasil diambil',
            'data' => ArtikelResource::collection($artikels)
        ], Response::HTTP_OK);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $artikel = Artikel::where('slug', $slug)
            ->where('status', 'published')
            ->where('tanggal_publish', '<=', now())
            ->with('kategori')
            ->first();
        
        if (!$artikel) {
            return response()->json([
                'success' => false,
                'message' => 'Artikel tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }
        
        // Increment views
        $artikel->increment('views');
        
        // Get related articles
        $relatedArticles = Artikel::where('kategori_id', $artikel->kategori_id)
            ->where('id', '!=', $artikel->id)
            ->where('status', 'published')
            ->limit(3)
            ->get();
        
        return response()->json([
            'success' => true,
            'message' => 'Detail artikel berhasil diambil',
            'data' => [
                'article' => new ArtikelResource($artikel),
                'related_articles' => ArtikelResource::collection($relatedArticles)
            ]
        ], Response::HTTP_OK);
    }
    
    /**
     * Get articles by kategori.
     */
    public function byKategori(string $kategoriSlug, Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $artikels = Artikel::whereHas('kategori', function($q) use ($kategoriSlug) {
                $q->where('slug', $kategoriSlug);
            })
            ->where('status', 'published')
            ->where('tanggal_publish', '<=', now())
            ->with('kategori')
            ->orderBy('tanggal_publish', 'desc')
            ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'message' => "Artikel dalam kategori {$kategoriSlug} berhasil diambil",
            'data' => [
                'data' => ArtikelResource::collection($artikels),
                'meta' => [
                    'current_page' => $artikels->currentPage(),
                    'per_page' => $artikels->perPage(),
                    'total' => $artikels->total(),
                    'last_page' => $artikels->lastPage(),
                ]
            ]
        ], Response::HTTP_OK);
    }
}