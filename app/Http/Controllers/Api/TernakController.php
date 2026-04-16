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
    /**
     * Get ternak by slug with category-specific data.
     */
    public function getBySlug($slug)
    {
        $ternak = Ternak::with([
            'fattening', 
            'perkawinanSebagaiBetina' => function($q) {
                $q->latest('tanggal_kawin')->with(['pejantan']);
            },
            'perkawinanSebagaiPejantan' => function($q) {
                $q->latest('tanggal_kawin')->with(['betina']);
            },
            'latestTimbangan',
            'riwayatTimbangs' => function($q) {
                $q->latest()->limit(5);
            }
        ])->where('slug', $slug)->first();
        
        if (!$ternak) {
            return response()->json([
                'success' => false,
                'message' => 'Ternak tidak ditemukan'
            ], 404);
        }
        
        $response = $ternak->toApiResponse();
        
        // Add category-specific data
        if ($ternak->kategori === 'fattening' && $ternak->fattening) {
            $response['data_kategori'] = [
                'type' => 'fattening',
                'program' => [
                    'bobot_awal' => $ternak->fattening->bobot_awal,
                    'bobot_terakhir' => $ternak->fattening->bobot_terakhir,
                    'target_bobot' => $ternak->fattening->target_bobot,
                    'tanggal_mulai' => $ternak->fattening->tanggal_mulai,
                    'tanggal_target_selesai' => $ternak->fattening->tanggal_target_selesai,
                    'status' => $ternak->fattening->status,
                    'progress_persen' => $this->calculateProgress($ternak->fattening),
                    'keterangan' => $ternak->fattening->keterangan,
                ]
            ];
        } elseif ($ternak->kategori === 'breeding') {
            $latestPerkawinan = $ternak->perkawinanSebagaiBetina->first() ?? $ternak->perkawinanSebagaiPejantan->first();
            
            if ($latestPerkawinan) {
                $response['data_kategori'] = [
                    'type' => 'breeding',
                    'perkawinan_terakhir' => [
                        'tanggal_kawin' => $latestPerkawinan->tanggal_kawin,
                        'jenis_kawin' => $latestPerkawinan->jenis_kawin,
                        'jenis_kawin_label' => $latestPerkawinan->jenis_kawin === 'alami' ? 'Alami' : 'Inseminasi Buatan',
                        'status_siklus' => $latestPerkawinan->status_siklus,
                        'status_label' => $this->getStatusLabel($latestPerkawinan->status_siklus),
                        'perkiraan_lahir' => $latestPerkawinan->perkiraan_lahir,
                        'pejantan' => $latestPerkawinan->pejantan ? [
                            'nama' => $latestPerkawinan->pejantan->nama_ternak,
                            'kode' => $latestPerkawinan->pejantan->kode_ternak,
                            'slug' => $latestPerkawinan->pejantan->slug,
                        ] : null,
                        'keterangan' => $latestPerkawinan->keterangan,
                    ]
                ];
                
                // Add gestation info if bunting
                if ($latestPerkawinan->status_siklus === 'bunting' && $latestPerkawinan->perkiraan_lahir) {
                    $response['data_kategori']['perkawinan_terakhir']['hari_menuju_lahir'] = now()->diffInDays($latestPerkawinan->perkiraan_lahir, false);
                }
            } else {
                $response['data_kategori'] = [
                    'type' => 'breeding',
                    'message' => 'Belum ada data perkawinan'
                ];
            }
        } else {
            $response['data_kategori'] = null;
        }
        
        // Add timbangan history
        $response['riwayat_timbangan'] = $ternak->riwayatTimbangs->map(function($timbang) {
            return [
                'tanggal' => $timbang->tanggal_timbang->format('Y-m-d'),
                'bobot' => $timbang->bobot,
                'keterangan' => $timbang->keterangan,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }
    
    private function calculateProgress($fattening)
    {
        if (!$fattening->target_bobot || $fattening->target_bobot <= 0) {
            return 0;
        }
        
        $currentBobot = $fattening->bobot_terakhir ?? $fattening->bobot_awal ?? 0;
        return round(($currentBobot / $fattening->target_bobot) * 100, 1);
    }
    
    private function getStatusLabel($status)
    {
        $labels = [
            'kosong' => 'Kosong',
            'kawin' => 'Kawin',
            'bunting' => 'Bunting',
            'gagal' => 'Gagal',
            'melahirkan' => 'Melahirkan'
        ];
        
        return $labels[$status] ?? $status;
    }
}