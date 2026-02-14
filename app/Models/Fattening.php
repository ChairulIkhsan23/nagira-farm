<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Fattening extends Model
{
    use HasFactory, SoftDeletes;

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
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fattening) {
            if (empty($fattening->bobot_terakhir)) {
                $fattening->bobot_terakhir = $fattening->bobot_awal;
            }
        });
    }

    /**
     * Accessor for progress percentage
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
        
        return min(100, round(($pertambahanSekarang / $targetPertambahan) * 100, 1));
    }

    /**
     * Accessor for sisa hari
     */
    public function getSisaHariAttribute(): int
    {
        if (!$this->tanggal_target_selesai || $this->status !== 'progres') {
            return 0;
        }
        
        $diff = now()->diffInDays($this->tanggal_target_selesai, false);
        return max(0, (int)$diff);
    }

    /**
     * Accessor for status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'progres' => 'warning',
            'selesai' => 'success',
            'gagal' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Accessor for ADG (Average Daily Gain) in grams
     */
    public function getAdgGramAttribute(): ?float
    {
        if (!$this->bobot_awal || !$this->bobot_terakhir || !$this->tanggal_mulai) {
            return null;
        }
        
        $hari = max(1, $this->tanggal_mulai->diffInDays($this->updated_at ?? now()));
        $pertambahan = $this->bobot_terakhir - $this->bobot_awal;
        
        // Convert kg to grams
        return round(($pertambahan / $hari) * 1000, 2);
    }

    /**
     * Accessor for ADG (Average Daily Gain) in kg
     */
    public function getAdgKgAttribute(): ?float
    {
        if (!$this->bobot_awal || !$this->bobot_terakhir || !$this->tanggal_mulai) {
            return null;
        }
        
        $hari = max(1, $this->tanggal_mulai->diffInDays($this->updated_at ?? now()));
        $pertambahan = $this->bobot_terakhir - $this->bobot_awal;
        
        return round($pertambahan / $hari, 3);
    }

    /**
     * Check if fattening is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'progres' 
            && $this->tanggal_target_selesai 
            && now()->gt($this->tanggal_target_selesai);
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the ternak that owns the fattening.
     */
    public function ternak()
    {
        return $this->belongsTo(Ternak::class, 'ternak_id');
    }

    /**
     * Get the riwayat timbangs for this fattening.
     */
    public function riwayatTimbangs()
    {
        return $this->hasMany(RiwayatTimbang::class, 'fattening_id');
    }

    /**
     * Get the latest timbangan for this fattening.
     */
    public function latestTimbangan()
    {
        return $this->hasOne(RiwayatTimbang::class, 'fattening_id')->latest('tanggal_timbang');
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to only include progres fattenings.
     */
    public function scopeProgres(Builder $query): Builder
    {
        return $query->where('status', 'progres');
    }

    /**
     * Scope a query to only include selesai fattenings.
     */
    public function scopeSelesai(Builder $query): Builder
    {
        return $query->where('status', 'selesai');
    }

    /**
     * Scope a query to only include gagal fattenings.
     */
    public function scopeGagal(Builder $query): Builder
    {
        return $query->where('status', 'gagal');
    }

    /**
     * Scope a query to only include overdue fattenings.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'progres')
            ->where('tanggal_target_selesai', '<', now());
    }

    /**
     * Scope a query to filter by date range mulai.
     */
    public function scopeMulaiAntara(Builder $query, $start, $end): Builder
    {
        return $query->whereBetween('tanggal_mulai', [$start, $end]);
    }
}