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
    public function index(Request $request)
    {
        $query = Ternak::query()
            ->where('status_aktif', 'aktif')
            ->with(['induk', 'pejantan']);
        
        
        if ($request->has('jenis_kelamin')) {
            $query->where('jenis_kelamin', $request->jenis_kelamin);
        }
        
        
        if ($request->has('jenis_ternak')) {
            $query->where('jenis_ternak', $request->jenis_ternak);
        }
        
        
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        
        
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

    public function featured(Request $request)
    {
        $limit = (int) $request->get('limit', 6);

        $featuredJenis = Ternak::query()
            ->where('status_aktif', 'aktif')
            ->latest()
            ->get();

        $data = $featuredJenis
            ->groupBy('jenis_ternak')
            ->take($limit)
            ->map(function ($ternaks, $jenisTernak) {
                $representative = $ternaks->first();

                return [
                    'jenis_ternak' => $jenisTernak,
                    'jenis_slug' => str($jenisTernak)->slug(),
                    'foto' => $representative?->foto ? asset('storage/' . $representative->foto) : null,
                    'jumlah_tersedia' => $ternaks->count(),
                    'price_range' => Ternak::priceRangeByJenis($jenisTernak),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Produk unggulan berhasil diambil',
            'data' => $data,
        ], Response::HTTP_OK);
    }

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
    
    
    public function getBySlug($slug)
    {
        $ternak = Ternak::query()
            ->where('slug', $slug)
            ->first();
        
        if (!$ternak) {
            return response()->json([
                'success' => false,
                'message' => 'Ternak tidak ditemukan'
            ], 404);
        }

        $response = $ternak->toPublicApiResponse();

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }
}
