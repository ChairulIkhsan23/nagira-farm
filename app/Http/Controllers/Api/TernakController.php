<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TernakCollection;
use App\Http\Resources\TernakResource;
use App\Models\Ternak;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TernakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ternak::query()
            ->where('status_aktif', 'aktif')
            ->with(['induk', 'pejantan']);
        
        // Filter by jenis kelamin
        if ($request->has('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        
        // Filter by jenis ternak
        if ($request->has('jenis_ternak')) {
            $query->where('jenis_ternak', $request->jenis_ternak);
        }
        
        // Filter by kategori
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        $perPage = $request->get('per_page', 12);
        $ternaks = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'message' => 'Data ternak berhasil diambil',
            'data' => new TernakCollection($ternaks)
        ], Response::HTTP_OK);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $ternak = Ternak::where('slug', $slug)
            ->where('status_aktif', 'aktif')
            ->with(['induk', 'pejantan', 'riwayatTimbangs' => function($q) {
                $q->latest()->limit(5);
            }, 'kesehatans' => function($q) {
                $q->latest()->limit(5);
            }])
            ->first();
        
        if (!$ternak) {
            return response()->json([
                'success' => false,
                'message' => 'Data ternak tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Detail ternak berhasil diambil',
            'data' => new TernakResource($ternak)
        ], Response::HTTP_OK);
    }
    
    /**
     * Get ternak by jenis.
     */
    public function byJenis(string $jenis, Request $request)
    {
        $perPage = $request->get('per_page', 12);
        
        $ternaks = Ternak::where('jenis_ternak', $jenis)
            ->where('status_aktif', 'aktif')
            ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'message' => "Data ternak jenis {$jenis} berhasil diambil",
            'data' => new TernakCollection($ternaks)
        ], Response::HTTP_OK);
    }
}