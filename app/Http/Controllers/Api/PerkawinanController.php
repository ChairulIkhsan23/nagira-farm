<?php

namespace App\Http\Controllers\Api;

use App\Models\Perkawinan;
use App\Models\Ternak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PerkawinanController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Perkawinan::with(['betina', 'pejantan']);
        
        
        if ($request->has('status_siklus')) {
            $query->where('status_siklus', $request->status_siklus);
        }
        
        
        if ($request->has('jenis_kawin')) {
            $query->where('jenis_kawin', $request->jenis_kawin);
        }
        
        
        if ($request->has('betina_id')) {
            $query->where('betina_id', $request->betina_id);
        }
        
        
        if ($request->has('tanggal_kawin_from')) {
            $query->where('tanggal_kawin', '>=', $request->tanggal_kawin_from);
        }
        
        if ($request->has('tanggal_kawin_to')) {
            $query->where('tanggal_kawin', '<=', $request->tanggal_kawin_to);
        }
        
        $perkawinans = $query->orderBy('tanggal_kawin', 'desc')->paginate(15);
        
        
        $perkawinans->getCollection()->transform(function ($perkawinan) {
            return $this->formatPerkawinanResponse($perkawinan);
        });
        
        return response()->json([
            'success' => true,
            'data' => $perkawinans
        ]);
    }
    
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'betina_id' => 'required|exists:ternaks,id',
            'pejantan_id' => 'nullable|exists:ternaks,id',
            'tanggal_kawin' => 'nullable|date',
            'jenis_kawin' => ['required', Rule::in(['alami', 'IB'])],
            'status_siklus' => ['nullable', Rule::in(['kosong', 'kawin', 'bunting', 'gagal', 'melahirkan'])],
            'perkiraan_lahir' => 'nullable|date|after_or_equal:tanggal_kawin',
            'keterangan' => 'nullable|string'
        ]);
        
        try {
            DB::beginTransaction();
            
            
            if (!isset($validated['status_siklus'])) {
                $validated['status_siklus'] = 'kawin';
            }
            
            
            if (empty($validated['perkiraan_lahir']) && !empty($validated['tanggal_kawin'])) {
                $validated['perkiraan_lahir'] = Carbon::parse($validated['tanggal_kawin'])->addMonths(9)->format('Y-m-d');
            }
            
            
            $betina = Ternak::find($validated['betina_id']);
            if ($betina && $betina->kategori !== 'breeding') {
                $betina->kategori = 'breeding';
                $betina->save();
            }
            
            $perkawinan = Perkawinan::create($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data perkawinan berhasil ditambahkan',
                'data' => $this->formatPerkawinanResponse($perkawinan->load(['betina', 'pejantan']))
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    public function show($id)
    {
        $perkawinan = Perkawinan::with(['betina', 'pejantan'])->find($id);
        
        if (!$perkawinan) {
            return response()->json([
                'success' => false,
                'message' => 'Data perkawinan tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $this->formatPerkawinanResponse($perkawinan)
        ]);
    }
    
    
    public function getByBetinaSlug($slug)
    {
        $betina = Ternak::where('slug', $slug)->where('jenis_kelamin', 'betina')->first();
        
        if (!$betina) {
            return response()->json([
                'success' => false,
                'message' => 'Ternak betina tidak ditemukan'
            ], 404);
        }
        
        $perkawinans = Perkawinan::where('betina_id', $betina->id)
            ->with(['betina', 'pejantan'])
            ->orderBy('tanggal_kawin', 'desc')
            ->get();
        
        $perkawinans->transform(function ($perkawinan) {
            return $this->formatPerkawinanResponse($perkawinan);
        });
        
        return response()->json([
            'success' => true,
            'data' => $perkawinans
        ]);
    }
    
    
    public function getActivePregnancies()
    {
        $pregnancies = Perkawinan::where('status_siklus', 'bunting')
            ->with(['betina', 'pejantan'])
            ->orderBy('perkiraan_lahir', 'asc')
            ->get();
        
        $pregnancies->transform(function ($pregnancy) {
            $formatted = $this->formatPerkawinanResponse($pregnancy);
            
            
            if ($pregnancy->perkiraan_lahir) {
                $formatted['hari_menuju_lahir'] = Carbon::now()->diffInDays(Carbon::parse($pregnancy->perkiraan_lahir), false);
                $formatted['minggu_kebuntingan'] = $pregnancy->tanggal_kawin 
                    ? floor(Carbon::parse($pregnancy->tanggal_kawin)->diffInWeeks(Carbon::now()))
                    : null;
            }
            
            return $formatted;
        });
        
        return response()->json([
            'success' => true,
            'data' => $pregnancies
        ]);
    }
    
    
    public function update(Request $request, $id)
    {
        $perkawinan = Perkawinan::find($id);
        
        if (!$perkawinan) {
            return response()->json([
                'success' => false,
                'message' => 'Data perkawinan tidak ditemukan'
            ], 404);
        }
        
        $validated = $request->validate([
            'pejantan_id' => 'nullable|exists:ternaks,id',
            'tanggal_kawin' => 'nullable|date',
            'jenis_kawin' => ['nullable', Rule::in(['alami', 'IB'])],
            'status_siklus' => ['nullable', Rule::in(['kosong', 'kawin', 'bunting', 'gagal', 'melahirkan'])],
            'perkiraan_lahir' => 'nullable|date|after_or_equal:tanggal_kawin',
            'keterangan' => 'nullable|string'
        ]);
        
        try {
            DB::beginTransaction();
            
            
            if (isset($validated['status_siklus']) && $validated['status_siklus'] === 'melahirkan') {
                if (empty($validated['perkiraan_lahir'])) {
                    $validated['perkiraan_lahir'] = Carbon::now()->format('Y-m-d');
                }
            }
            
            $perkawinan->update($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Data perkawinan berhasil diperbarui',
                'data' => $this->formatPerkawinanResponse($perkawinan->load(['betina', 'pejantan']))
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    public function updatePregnancyStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status_siklus' => ['required', Rule::in(['kosong', 'kawin', 'bunting', 'gagal', 'melahirkan'])],
            'perkiraan_lahir' => 'nullable|date',
            'keterangan' => 'nullable|string'
        ]);
        
        $perkawinan = Perkawinan::find($id);
        
        if (!$perkawinan) {
            return response()->json([
                'success' => false,
                'message' => 'Data perkawinan tidak ditemukan'
            ], 404);
        }
        
        $perkawinan->status_siklus = $validated['status_siklus'];
        
        if (isset($validated['perkiraan_lahir'])) {
            $perkawinan->perkiraan_lahir = $validated['perkiraan_lahir'];
        }
        
        if (isset($validated['keterangan'])) {
            $perkawinan->keterangan = $validated['keterangan'];
        }
        
        
        if ($validated['status_siklus'] === 'melahirkan' && !$perkawinan->perkiraan_lahir) {
            $perkawinan->perkiraan_lahir = Carbon::now()->format('Y-m-d');
        }
        
        $perkawinan->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Status kebuntingan berhasil diperbarui',
            'data' => $this->formatPerkawinanResponse($perkawinan->load(['betina', 'pejantan']))
        ]);
    }
    
    
    public function destroy($id)
    {
        $perkawinan = Perkawinan::find($id);
        
        if (!$perkawinan) {
            return response()->json([
                'success' => false,
                'message' => 'Data perkawinan tidak ditemukan'
            ], 404);
        }
        
        $perkawinan->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Data perkawinan berhasil dihapus'
        ]);
    }
    
    
    public function statistics()
    {
        $stats = [
            'total_perkawinan' => Perkawinan::count(),
            'status_distribution' => [
                'kosong' => Perkawinan::where('status_siklus', 'kosong')->count(),
                'kawin' => Perkawinan::where('status_siklus', 'kawin')->count(),
                'bunting' => Perkawinan::where('status_siklus', 'bunting')->count(),
                'gagal' => Perkawinan::where('status_siklus', 'gagal')->count(),
                'melahirkan' => Perkawinan::where('status_siklus', 'melahirkan')->count(),
            ],
            'jenis_kawin' => [
                'alami' => Perkawinan::where('jenis_kawin', 'alami')->count(),
                'IB' => Perkawinan::where('jenis_kawin', 'IB')->count(),
            ],
            'success_rate' => 0,
            'average_gestation_days' => 0,
            'induk_aktif' => Ternak::where('jenis_kelamin', 'betina')
                ->where('kategori', 'breeding')
                ->where('status_aktif', 'aktif')
                ->count()
        ];
        
        
        $totalPregnancies = Perkawinan::whereIn('status_siklus', ['bunting', 'melahirkan', 'gagal'])->count();
        $totalBirths = Perkawinan::where('status_siklus', 'melahirkan')->count();
        
        if ($totalPregnancies > 0) {
            $stats['success_rate'] = round(($totalBirths / $totalPregnancies) * 100, 2);
        }
        
        
        $completedPregnancies = Perkawinan::where('status_siklus', 'melahirkan')
            ->whereNotNull('tanggal_kawin')
            ->whereNotNull('perkiraan_lahir')
            ->get();
        
        if ($completedPregnancies->count() > 0) {
            $totalDays = $completedPregnancies->sum(function ($p) {
                return Carbon::parse($p->tanggal_kawin)->diffInDays(Carbon::parse($p->perkiraan_lahir));
            });
            $stats['average_gestation_days'] = round($totalDays / $completedPregnancies->count(), 1);
        }
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    
    private function formatPerkawinanResponse($perkawinan)
    {
        if (!$perkawinan) return null;
        
        $data = $perkawinan->toArray();
        
        
        $statusLabels = [
            'kosong' => 'Kosong',
            'kawin' => 'Kawin',
            'bunting' => 'Bunting',
            'gagal' => 'Gagal Kawin',
            'melahirkan' => 'Melahirkan'
        ];
        $data['status_siklus_label'] = $statusLabels[$perkawinan->status_siklus] ?? $perkawinan->status_siklus;
        
        
        $jenisKawinLabels = [
            'alami' => 'Alami',
            'IB' => 'Inseminasi Buatan'
        ];
        $data['jenis_kawin_label'] = $jenisKawinLabels[$perkawinan->jenis_kawin] ?? $perkawinan->jenis_kawin;
        
        
        if ($perkawinan->status_siklus === 'bunting' && $perkawinan->tanggal_kawin && $perkawinan->perkiraan_lahir) {
            $start = Carbon::parse($perkawinan->tanggal_kawin);
            $end = Carbon::parse($perkawinan->perkiraan_lahir);
            $now = Carbon::now();
            
            $totalDays = $start->diffInDays($end);
            $daysPassed = $start->diffInDays($now);
            
            if ($totalDays > 0) {
                $data['gestation_progress'] = round(($daysPassed / $totalDays) * 100, 1);
                $data['gestation_days_passed'] = $daysPassed;
                $data['gestation_days_remaining'] = max(0, $totalDays - $daysPassed);
            }
        }
        
        return $data;
    }
}