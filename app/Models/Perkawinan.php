<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Perkawinan extends Model
{
    use HasFactory;

    protected $table = 'perkawinans';

    protected $fillable = [
        'betina_id',
        'pejantan_id',
        'tanggal_kawin',
        'jenis_kawin',
        'status_siklus',
        'perkiraan_lahir',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_kawin' => 'date',
        'perkiraan_lahir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [
        'status_siklus' => 'kawin',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($perkawinan) {

            // Update betina
            if ($perkawinan->betina_id) {
                Ternak::where('id', $perkawinan->betina_id)
                    ->update(['kategori' => 'breeding']);
            }

            // Update pejantan
            if ($perkawinan->pejantan_id) {
                Ternak::where('id', $perkawinan->pejantan_id)
                    ->update(['kategori' => 'breeding']);
            }
        });

        static::creating(function ($perkawinan) {
            if ($perkawinan->tanggal_kawin && !$perkawinan->perkiraan_lahir) {
                $perkawinan->perkiraan_lahir = Carbon::parse($perkawinan->tanggal_kawin)->addDays(150);
            }
        });
    }
    /**
     * Accessor for sisa hari kebuntingan
     */
    public function getSisaHariKebuntinganAttribute(): ?int
    {
        if ($this->status_siklus !== 'bunting' || !$this->perkiraan_lahir) {
            return null;
        }
        
        $diff = now()->diffInDays($this->perkiraan_lahir, false);
        return (int)$diff;
    }

    /**
     * Accessor for status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status_siklus) {
            'kosong' => 'gray',
            'kawin' => 'warning',
            'bunting' => 'success',
            'gagal' => 'danger',
            'melahirkan' => 'info',
            default => 'gray',
        };
    }

    /**
     * Accessor for status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status_siklus) {
            'kosong' => 'Kosong',
            'kawin' => 'Kawin',
            'bunting' => 'Bunting',
            'gagal' => 'Gagal',
            'melahirkan' => 'Melahirkan',
            default => 'Unknown',
        };
    }

    /**
     * Accessor for jenis kawin label
     */
    public function getJenisKawinLabelAttribute(): string
    {
        return $this->jenis_kawin === 'alami' ? 'Alami' : 'Inseminasi Buatan (IB)';
    }

    /**
     * Check if kebuntingan is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status_siklus === 'bunting' 
            && $this->perkiraan_lahir 
            && now()->gt($this->perkiraan_lahir);
    }

    /**
     * Check if near due date (within 7 days)
     */
    public function getIsNearDueAttribute(): bool
    {
        if ($this->status_siklus !== 'bunting' || !$this->perkiraan_lahir) {
            return false;
        }
        
        $daysUntil = now()->diffInDays($this->perkiraan_lahir, false);
        return $daysUntil > 0 && $daysUntil <= 7;
    }

    /**
     * Calculate umur kebuntingan in days
     */
    public function getUmurKebuntinganHariAttribute(): ?int
    {
        if ($this->status_siklus !== 'bunting' || !$this->tanggal_kawin) {
            return null;
        }
        
        return $this->tanggal_kawin->diffInDays(now());
    }

    /**
     * Calculate umur kebuntingan in months
     */
    public function getUmurKebuntinganBulanAttribute(): ?float
    {
        if ($this->status_siklus !== 'bunting' || !$this->tanggal_kawin) {
            return null;
        }
        
        return round($this->tanggal_kawin->diffInMonths(now()), 1);
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the betina (female) ternak.
     */
    public function betina()
    {
        return $this->belongsTo(Ternak::class, 'betina_id');
    }

    /**
     * Get the pejantan (male) ternak.
     */
    public function pejantan()
    {
        return $this->belongsTo(Ternak::class, 'pejantan_id');
    }

    /**
     * Get the kelahiran record associated with this perkawinan.
     */
    public function kelahiran()
    {
        return $this->hasOne(Kelahiran::class, 'perkawinan_id');
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to only include bunting status.
     */
    public function scopeBunting(Builder $query): Builder
    {
        return $query->where('status_siklus', 'bunting');
    }

    /**
     * Scope a query to only include kawin status.
     */
    public function scopeKawin(Builder $query): Builder
    {
        return $query->where('status_siklus', 'kawin');
    }

    /**
     * Scope a query to only include gagal status.
     */
    public function scopeGagal(Builder $query): Builder
    {
        return $query->where('status_siklus', 'gagal');
    }

    /**
     * Scope a query to only include melahirkan status.
     */
    public function scopeMelahirkan(Builder $query): Builder
    {
        return $query->where('status_siklus', 'melahirkan');
    }

    /**
     * Scope a query to only include IB (Inseminasi Buatan).
     */
    public function scopeIb(Builder $query): Builder
    {
        return $query->where('jenis_kawin', 'IB');
    }

    /**
     * Scope a query to only include alami.
     */
    public function scopeAlami(Builder $query): Builder
    {
        return $query->where('jenis_kawin', 'alami');
    }

    /**
     * Scope a query to only include overdue bunting.
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status_siklus', 'bunting')
            ->where('perkiraan_lahir', '<', now());
    }

    /**
     * Scope a query to only include near due bunting.
     */
    public function scopeNearDue(Builder $query, int $days = 7): Builder
    {
        return $query->where('status_siklus', 'bunting')
            ->whereBetween('perkiraan_lahir', [now(), now()->addDays($days)]);
    }

    /**
     * Scope a query for a specific betina.
     */
    public function scopeForBetina(Builder $query, $betinaId): Builder
    {
        return $query->where('betina_id', $betinaId);
    }
}