<?php

namespace App\Http\Controllers\Api;

use App\Models\Fattening;
use App\Models\Ternak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FatteningController extends Controller
{
    /**
     * Display a listing of fattenings.
     */
    public function index(Request $request)
    {
        $query = Fattening::with(['ternak' => function($q) {
            $q->with(['latestTimbangan']);
        }]);
        
        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by ternak
        if ($request->has('ternak_id')) {
            $query->where('ternak_id', $request->ternak_id);
        }
        
        // Filter by date range
        if ($request->has('tanggal_mulai_from')) {
            $query->where('tanggal_mulai', '>=', $request->tanggal_mulai_from);
        }
        
        if ($request->has('tanggal_mulai_to')) {
            $query->where('tanggal_mulai', '<=', $request->tanggal_mulai_to);
        }
        
        $fattenings = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Add additional data for each fattening
        $fattenings->getCollection()->transform(function ($fattening) {
            return $this->formatFatteningResponse($fattening);
        });
        
        return response()->json([
            'success' => true,
            'data' => $fattenings
        ]);
    }
    
    /**
     * Store a newly created fattening.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ternak_id' => 'required|exists:ternaks,id|unique:fattenings,ternak_id',
            'bobot_awal' => 'nullable|numeric|min:0',
            'target_bobot' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_target_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Set default values
            $validated['status'] = 'progres';
            $validated['bobot_terakhir'] = $validated['bobot_awal'] ?? null;
            
            // Update ternak kategori to fattening if not already
            $ternak = Ternak::find($validated['ternak_id']);
            if ($ternak && $ternak->kategori !== 'fattening') {
                $ternak->kategori = 'fattening';
                $ternak->save();
            }
            
            $fattening = Fattening::create($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data penggemukan berhasil ditambahkan',
                'data' => $this->formatFatteningResponse($fattening->load('ternak'))
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified fattening.
     */
    public function show($id)
    {
        $fattening = Fattening::with(['ternak' => function($q) {
            $q->with(['latestTimbangan', 'riwayatTimbangs']);
        }])->find($id);
        
        if (!$fattening) {
            return response()->json([
                'success' => false,
                'message' => 'Data penggemukan tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $this->formatFatteningResponse($fattening)
        ]);
    }
    
    /**
     * Get fattening by ternak slug.
     */
    public function getByTernakSlug($slug)
    {
        $ternak = Ternak::where('slug', $slug)->first();
        
        if (!$ternak) {
            return response()->json([
                'success' => false,
                'message' => 'Ternak tidak ditemukan'
            ], 404);
        }
        
        $fattening = Fattening::where('ternak_id', $ternak->id)
            ->with(['ternak' => function($q) {
                $q->with(['latestTimbangan', 'riwayatTimbangs']);
            }])
            ->first();
        
        if (!$fattening) {
            return response()->json([
                'success' => false,
                'message' => 'Data penggemukan tidak ditemukan untuk ternak ini'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $this->formatFatteningResponse($fattening)
        ]);
    }
    
    /**
     * Update the specified fattening.
     */
    public function update(Request $request, $id)
    {
        $fattening = Fattening::find($id);
        
        if (!$fattening) {
            return response()->json([
                'success' => false,
                'message' => 'Data penggemukan tidak ditemukan'
            ], 404);
        }
        
        $validated = $request->validate([
            'bobot_awal' => 'nullable|numeric|min:0',
            'bobot_terakhir' => 'nullable|numeric|min:0',
            'target_bobot' => 'nullable|numeric|min:0',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_target_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'status' => ['nullable', Rule::in(['progres', 'selesai', 'gagal'])],
            'keterangan' => 'nullable|string'
        ]);
        
        try {
            DB::beginTransaction();
            
            $fattening->update($validated);
            
            // If status is 'selesai' and bobot_terakhir not set, use latest bobot from timbangan
            if ($fattening->status === 'selesai' && !$fattening->bobot_terakhir) {
                $latestBobot = $fattening->ternak?->latest_bobot;
                if ($latestBobot) {
                    $fattening->bobot_terakhir = $latestBobot;
                    $fattening->save();
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data penggemukan berhasil diperbarui',
                'data' => $this->formatFatteningResponse($fattening->load('ternak'))
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update bobot terakhir for fattening.
     */
    public function updateBobot(Request $request, $id)
    {
        $validated = $request->validate([
            'bobot_terakhir' => 'required|numeric|min:0'
        ]);
        
        $fattening = Fattening::find($id);
        
        if (!$fattening) {
            return response()->json([
                'success' => false,
                'message' => 'Data penggemukan tidak ditemukan'
            ], 404);
        }
        
        $fattening->bobot_terakhir = $validated['bobot_terakhir'];
        $fattening->save();
        
        // Check if target achieved
        if ($fattening->target_bobot && $fattening->bobot_terakhir >= $fattening->target_bobot) {
            $fattening->status = 'selesai';
            $fattening->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Bobot terakhir berhasil diperbarui',
            'data' => $this->formatFatteningResponse($fattening->load('ternak'))
        ]);
    }
    
    /**
     * Remove the specified fattening.
     */
    public function destroy($id)
    {
        $fattening = Fattening::find($id);
        
        if (!$fattening) {
            return response()->json([
                'success' => false,
                'message' => 'Data penggemukan tidak ditemukan'
            ], 404);
        }
        
        $fattening->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Data penggemukan berhasil dihapus'
        ]);
    }
    
    /**
     * Get statistics for fattening dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total' => Fattening::count(),
            'progres' => Fattening::where('status', 'progres')->count(),
            'selesai' => Fattening::where('status', 'selesai')->count(),
            'gagal' => Fattening::where('status', 'gagal')->count(),
            'rata_rata_pertumbuhan' => 0,
            'rata_rata_lama_penggemukan' => 0,
            'persentase_keberhasilan' => 0
        ];
        
        // Calculate average growth for completed fattenings
        $completedFattenings = Fattening::where('status', 'selesai')
            ->whereNotNull('bobot_awal')
            ->whereNotNull('bobot_terakhir')
            ->get();
        
        if ($completedFattenings->count() > 0) {
            $totalGrowth = $completedFattenings->sum(function ($f) {
                return $f->bobot_terakhir - $f->bobot_awal;
            });
            $stats['rata_rata_pertumbuhan'] = round($totalGrowth / $completedFattenings->count(), 2);
            
            // Calculate average duration
            $totalDays = $completedFattenings->sum(function ($f) {
                if ($f->tanggal_mulai && $f->tanggal_target_selesai) {
                    return \Carbon\Carbon::parse($f->tanggal_mulai)->diffInDays($f->tanggal_target_selesai);
                }
                return 0;
            });
            $stats['rata_rata_lama_penggemukan'] = round($totalDays / $completedFattenings->count(), 1);
            
            // Calculate success rate
            $totalCompleted = Fattening::whereIn('status', ['selesai', 'gagal'])->count();
            if ($totalCompleted > 0) {
                $stats['persentase_keberhasilan'] = round(($stats['selesai'] / $totalCompleted) * 100, 2);
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Format fattening response with additional data.
     */
    private function formatFatteningResponse($fattening)
    {
        if (!$fattening) return null;
        
        $data = $fattening->toArray();
        
        // Add progress percentage
        if ($fattening->target_bobot && $fattening->target_bobot > 0) {
            $currentBobot = $fattening->bobot_terakhir ?? $fattening->bobot_awal ?? 0;
            $data['progress_persen'] = round(($currentBobot / $fattening->target_bobot) * 100, 1);
        } else {
            $data['progress_persen'] = 0;
        }
        
        // Add days remaining
        if ($fattening->tanggal_target_selesai && $fattening->status === 'progres') {
            $daysRemaining = \Carbon\Carbon::now()->diffInDays($fattening->tanggal_target_selesai, false);
            $data['hari_tersisa'] = $daysRemaining > 0 ? $daysRemaining : 0;
        } else {
            $data['hari_tersisa'] = null;
        }
        
        // Add bobot gain
        if ($fattening->bobot_awal && $fattening->bobot_terakhir) {
            $data['selisih_bobot'] = $fattening->bobot_terakhir - $fattening->bobot_awal;
        } else {
            $data['selisih_bobot'] = null;
        }
        
        return $data;
    }
}