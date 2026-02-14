<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class RiwayatTimbang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'riwayat_timbangs';

    protected $fillable = [
        'ternak_id',
        'bobot',
        'fattening_id',
        'tanggal_timbang',
        'catatan',
    ];

    protected $casts = [
        'bobot' => 'float',
        'tanggal_timbang' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($timbang) {
            if ($timbang->fattening_id) {
                $timbang->fattening()->update([
                    'bobot_terakhir' => $timbang->bobot,
                ]);
            }
        });
    }

    /**
     * Accessor for formatted bobot
     */
    public function getBobotFormattedAttribute(): string
    {
        return number_format($this->bobot, 2) . ' kg';
    }

    /**
     * Accessor for formatted tanggal
     */
    public function getTanggalFormattedAttribute(): string
    {
        return $this->tanggal_timbang->format('d M Y H:i');
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the ternak that owns the timbangan.
     */
    public function ternak()
    {
        return $this->belongsTo(Ternak::class, 'ternak_id');
    }

    /**
     * Get the fattening that owns the timbangan.
     */
    public function fattening()
    {
        return $this->belongsTo(Fattening::class, 'fattening_id');
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('tanggal_timbang', 'desc');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('tanggal_timbang', [$startDate, $endDate]);
    }

    /**
     * Scope a query for a specific ternak.
     */
    public function scopeForTernak(Builder $query, $ternakId): Builder
    {
        return $query->where('ternak_id', $ternakId);
    }
}