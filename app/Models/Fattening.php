<?php

namespace App\Models;

use App\Enums\StatusFattening;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fattening extends Model
{
    use HasFactory;

    protected $table = 'fattenings';

    protected $fillable = [
        'ternak_id',
        'bobot_awal',
        'bobot_terakhir',
        'target_bobot',
        'tanggal_mulai',
        'tanggal_target_selesai',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'bobot_awal' => 'float',
        'bobot_terakhir' => 'float',
        'target_bobot' => 'float',
        'tanggal_mulai' => 'date',
        'tanggal_target_selesai' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'progres',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Fattening $fattening) {
            // Set bobot_terakhir sama dengan bobot_awal jika tidak diisi
            if (empty($fattening->bobot_terakhir) && !empty($fattening->bobot_awal)) {
                $fattening->bobot_terakhir = $fattening->bobot_awal;
            }
        });

        static::created(function (Fattening $fattening) {
            // Buat riwayat timbang pertama secara otomatis
            if ($fattening->bobot_awal && $fattening->tanggal_mulai) {
                $fattening->riwayatTimbangs()->create([
                    'ternak_id' => $fattening->ternak_id,
                    'bobot' => $fattening->bobot_awal,
                    'tanggal_timbang' => $fattening->tanggal_mulai->startOfDay(),
                    'catatan' => 'Bobot awal program fattening',
                    'fattening_id' => $fattening->id,
                ]);
            }
        });

        static::updating(function (Fattening $fattening) {
            // Auto-update status jika bobot berubah dan status belum final
            if ($fattening->isDirty('bobot_terakhir') && !in_array($fattening->status, ['selesai', 'gagal'])) {
                $fattening->checkAndUpdateStatus();
            }
        });
    }

    // ===================== BUSINESS LOGIC =====================

    /**
     * Check and update status based on progress
     */
    public function checkAndUpdateStatus(): void
    {
        // Jika status sudah selesai atau gagal, jangan ubah
        if (in_array($this->status, ['selesai', 'gagal'])) {
            return;
        }

        $statusChanged = false;

        // Cek apakah target bobot tercapai
        if ($this->target_bobot && $this->bobot_terakhir >= $this->target_bobot) {
            $this->status = 'selesai';
            $statusChanged = true;
        }
        // Cek apakah overdue (melewati target tanggal) dan belum mencapai target
        elseif ($this->tanggal_target_selesai && 
                $this->tanggal_target_selesai->isPast() && 
                $this->bobot_terakhir < $this->target_bobot) {
            $this->status = 'gagal';
            $statusChanged = true;
        }

        // Simpan hanya jika status berubah
        if ($statusChanged) {
            // Gunakan saveQuietly agar tidak trigger updating lagi
            $this->saveQuietly();
            
            if ($this->ternak) {
                // Cek apakah masih ada program aktif lain
                $hasActiveFattening = self::where('ternak_id', $this->ternak_id)
                    ->where('status', 'progres')
                    ->where('id', '!=', $this->id)
                    ->exists();
                
                if (!$hasActiveFattening) {
                    $this->ternak->kategori = 'regular';
                    $this->ternak->saveQuietly();
                }
            }
        }
    }

    /**
     * Update bobot dan buat riwayat timbang baru
     */
    public function updateBobot(float $bobotBaru, string $catatan = null, $tanggalTimbang = null): RiwayatTimbang
    {
        $tanggal = $tanggalTimbang ?? now();
        
        // Validasi bobot baru tidak boleh lebih kecil dari bobot sebelumnya
        if ($bobotBaru < $this->bobot_terakhir) {
            throw new \Exception('Bobot baru tidak boleh lebih kecil dari bobot terakhir');
        }
        
        // Buat riwayat timbang
        $riwayat = $this->riwayatTimbangs()->create([
            'ternak_id' => $this->ternak_id,
            'bobot' => $bobotBaru,
            'tanggal_timbang' => $tanggal,
            'catatan' => $catatan ?? 'Penimbangan rutin',
            'fattening_id' => $this->id,
        ]);

        // Update bobot terakhir (akan trigger updating event)
        $this->update([
            'bobot_terakhir' => $bobotBaru
        ]);

        return $riwayat;
    }

    /**
     * Batalkan program fattening dengan status gagal
     */
    public function batalkan(string $alasan): void
    {
        $this->update([
            'status' => 'gagal',
            'keterangan' => $this->keterangan . "\n[DIBATALKAN: " . $alasan . "]"
        ]);
    }

    /**
     * Selesaikan program fattening dengan sukses
     */
    public function selesaikan(): void
    {
        if ($this->bobot_terakhir < $this->target_bobot) {
            throw new \Exception('Tidak dapat menyelesaikan program karena target bobot belum tercapai');
        }
        
        $this->update(['status' => 'selesai']);
    }

    // ===================== ACCESSORS & ATTRIBUTES =====================

    /**
     * Get formatted ID with prefix (FAT-00001)
     */
    public function getFormattedIdAttribute(): string
    {
        return 'FAT-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get progress percentage
     */
    public function getProgressPersenAttribute(): float
    {
        if (!$this->bobot_awal || !$this->bobot_terakhir || !$this->target_bobot) {
            return 0;
        }
        
        $targetPertambahan = $this->target_bobot - $this->bobot_awal;
        $pertambahanSekarang = $this->bobot_terakhir - $this->bobot_awal;
        
        if ($targetPertambahan <= 0) {
            return 100;
        }
        
        if ($pertambahanSekarang <= 0) {
            return 0;
        }
        
        $progress = ($pertambahanSekarang / $targetPertambahan) * 100;
        return min(100, round($progress, 1));
    }

    /**
     * Get remaining days
     */
    public function getSisaHariAttribute(): int
    {
        if (!$this->tanggal_target_selesai) {
            return 0;
        }
        
        // Jika sudah selesai atau gagal, sisa hari 0
        if (in_array($this->status, ['selesai', 'gagal'])) {
            return 0;
        }
        
        $today = now()->startOfDay();
        $targetDate = $this->tanggal_target_selesai->startOfDay();
        
        if ($today->greaterThan($targetDate)) {
            return 0;
        }
        
        return $today->diffInDays($targetDate);
    }

    /**
     * Get overdue days (jumlah hari terlambat)
     */
    public function getHariTerlambatAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        
        return $this->tanggal_target_selesai->startOfDay()->diffInDays(now()->startOfDay());
    }

    /**
     * Check if fattening is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'progres' 
            && $this->tanggal_target_selesai 
            && now()->startOfDay()->gt($this->tanggal_target_selesai->startOfDay());
    }

    /**
     * Get total weight gain
     */
    public function getTotalPertambahanAttribute(): float
    {
        return round($this->bobot_terakhir - $this->bobot_awal, 1);
    }

    /**
     * Get average daily gain (ADG)
     */
    public function getAdgAttribute(): float
    {
        if (!$this->tanggal_mulai) {
            return 0;
        }
        
        $hariBerjalan = max(1, $this->tanggal_mulai->diffInDays(now()));
        $totalGain = $this->total_pertambahan;
        
        if ($totalGain <= 0) {
            return 0;
        }
        
        return round($totalGain / $hariBerjalan, 2);
    }

    /**
     * Get estimated days to reach target
     */
    public function getEstimasiHariTercapaiAttribute(): ?int
    {
        $adg = $this->adg;
        if ($adg <= 0 || $this->status !== 'progres') {
            return null;
        }
        
        $sisaTarget = $this->target_bobot - $this->bobot_terakhir;
        if ($sisaTarget <= 0) {
            return 0;
        }
        
        return ceil($sisaTarget / $adg);
    }

    /**
     * Get estimated completion date
     */
    public function getEstimasiTanggalSelesaiAttribute(): ?string
    {
        $estimasiHari = $this->estimasi_hari_tercapai;
        if (!$estimasiHari) {
            return null;
        }
        
        return now()->addDays($estimasiHari)->format('d M Y');
    }

    /**
     * Get status label with color for filament
     */
    public function getStatusLabelAttribute(): array
    {
        return match($this->status) {
            'progres' => ['label' => 'Dalam Progress', 'color' => 'warning'],
            'selesai' => ['label' => 'Selesai', 'color' => 'success'],
            'gagal' => ['label' => 'Gagal', 'color' => 'danger'],
            default => ['label' => ucfirst($this->status), 'color' => 'gray'],
        };
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the ternak that owns the fattening.
     */
    public function ternak(): BelongsTo
    {
        return $this->belongsTo(Ternak::class, 'ternak_id');
    }

    /**
     * Get all weighing history for this fattening.
     */
    public function riwayatTimbangs(): HasMany
    {
        return $this->hasMany(RiwayatTimbang::class, 'fattening_id');
    }

    /**
     * Get the latest weighing record.
     */
    public function timbangTerakhir(): HasOne
    {
        return $this->hasOne(RiwayatTimbang::class, 'fattening_id')
            ->latest('tanggal_timbang');
    }

    /**
     * Get the first weighing record.
     */
    public function timbangPertama(): HasOne
    {
        return $this->hasOne(RiwayatTimbang::class, 'fattening_id')
            ->oldest('tanggal_timbang');
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to only include active fattenings (progres)
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'progres');
    }

    /**
     * Scope a query to only include completed fattenings
     */
    public function scopeSelesai(Builder $query): Builder
    {
        return $query->where('status', 'selesai');
    }

    /**
     * Scope a query to only include failed fattenings
     */
    public function scopeGagal(Builder $query): Builder
    {
        return $query->where('status', 'gagal');
    }

    /**
     * Scope a query to only include overdue fattenings
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'progres')
            ->whereDate('tanggal_target_selesai', '<', now());
    }

    /**
     * Scope a query to only include fattenings with progress above certain percentage
     */
    public function scopeProgressMinimal(Builder $query, float $persen): Builder
    {
        return $query->where('status', 'progres')
            ->whereRaw('((bobot_terakhir - bobot_awal) / (target_bobot - bobot_awal) * 100) >= ?', [$persen]);
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeTanggalMulaiAntara(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('tanggal_mulai', [$startDate, $endDate]);
    }

    /**
     * Scope a query to filter by target date range
     */
    public function scopeTargetSelesaiAntara(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('tanggal_target_selesai', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include fattenings that are near target (within 10% of target)
     */
    public function scopeHampirTercapai(Builder $query): Builder
    {
        return $query->where('status', 'progres')
            ->whereRaw('((bobot_terakhir - bobot_awal) / (target_bobot - bobot_awal) * 100) >= 90');
    }

    // ===================== UTILITY METHODS =====================

    /**
     * Check if target is achievable based on current ADG
     */
    public function isTargetAchievable(): bool
    {
        if ($this->status !== 'progres' || !$this->tanggal_target_selesai) {
            return false;
        }
        
        $sisaHari = $this->tanggal_target_selesai->diffInDays(now());
        if ($sisaHari <= 0) {
            return false;
        }
        
        $sisaTarget = $this->target_bobot - $this->bobot_terakhir;
        if ($sisaTarget <= 0) {
            return true;
        }
        
        $adg = $this->adg;
        if ($adg <= 0) {
            return false;
        }
        
        return ($adg * $sisaHari) >= $sisaTarget;
    }

    /**
     * Get recommended feeding strategy based on progress
     */
    public function getRecommendedStrategy(): string
    {
        if ($this->status !== 'progres') {
            return 'Program sudah selesai atau gagal';
        }
        
        $progress = $this->progress_persen;
        $isOverdue = $this->is_overdue;
        $adg = $this->adg;
        $targetAdg = ($this->target_bobot - $this->bobot_awal) / 
                     max(1, $this->tanggal_mulai->diffInDays($this->tanggal_target_selesai));
        
        if ($isOverdue) {
            return "Program terlambat. Pertimbangkan evaluasi ulang target atau strategi pakan.";
        }
        
        if ($progress < 30) {
            return "Fase adaptasi awal. Fokus pada kesehatan ternak dan pembiasaan pakan.";
        } elseif ($progress < 70) {
            if ($adg < $targetAdg * 0.8) {
                return "Pertumbuhan di bawah target. Tingkatkan kualitas dan kuantitas pakan.";
            }
            return "Pertumbuhan sesuai target. Pertahankan manajemen pakan saat ini.";
        } else {
            if ($this->bobot_terakhir >= $this->target_bobot) {
                return "Target tercapai! Siapkan untuk fase finishing.";
            }
            return "Fase akhir penggemukan. Optimalkan pakan untuk mencapai target.";
        }
    }
}