<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Kesehatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kesehatans';

    protected $fillable = [
        'ternak_id',
        'kondisi',
        'diagnosa',
        'tindakan',
        'obat',
        'tanggal_periksa',
    ];

    protected $casts = [
        'tanggal_periksa' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Accessor for kondisi badge color
     */
    public function getKondisiBadgeColorAttribute(): string
    {
        return match($this->kondisi) {
            'sehat' => 'success',
            'sakit' => 'warning',
            'kritis' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Accessor for kondisi label
     */
    public function getKondisiLabelAttribute(): string
    {
        return match($this->kondisi) {
            'sehat' => 'Sehat',
            'sakit' => 'Sakit',
            'kritis' => 'Kritis',
            default => 'Unknown',
        };
    }

    /**
     * Accessor for formatted tanggal periksa
     */
    public function getTanggalPeriksaFormattedAttribute(): string
    {
        return $this->tanggal_periksa ? $this->tanggal_periksa->format('d M Y') : '-';
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the ternak that owns the kesehatan record.
     */
    public function ternak()
    {
        return $this->belongsTo(Ternak::class, 'ternak_id');
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to only include sehat records.
     */
    public function scopeSehat(Builder $query): Builder
    {
        return $query->where('kondisi', 'sehat');
    }

    /**
     * Scope a query to only include sakit records.
     */
    public function scopeSakit(Builder $query): Builder
    {
        return $query->where('kondisi', 'sakit');
    }

    /**
     * Scope a query to only include kritis records.
     */
    public function scopeKritis(Builder $query): Builder
    {
        return $query->where('kondisi', 'kritis');
    }

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('tanggal_periksa', 'desc');
    }

    /**
     * Scope a query for a specific ternak.
     */
    public function scopeForTernak(Builder $query, $ternakId): Builder
    {
        return $query->where('ternak_id', $ternakId);
    }
}