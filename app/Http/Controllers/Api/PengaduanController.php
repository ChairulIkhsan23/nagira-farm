<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePengaduanRequest;
use App\Http\Resources\PengaduanResource;
use App\Models\Pengaduan;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PengaduanController extends Controller
{
    
    public function store(StorePengaduanRequest $request)
    {
        try {
            $pengaduan = Pengaduan::create([
                'nama_pengirim' => $request->nama_pengirim,
                'email' => $request->email,
                'kategori' => $request->kategori,
                'subjek' => $request->subjek,
                'pesan' => $request->pesan,
            ]);
            
            
            
            
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih, pesan Anda telah kami terima',
                'data' => new PengaduanResource($pengaduan)
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            Log::error('Error storing pengaduan: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan, silakan coba lagi'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}