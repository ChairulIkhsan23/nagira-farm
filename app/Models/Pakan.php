<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Pakan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pakans';

    protected $fillable = [
        'ternak_id',
        'jenis_pakan',
        'jumlah_pakan',
        'tanggal_pemberian',
        'catatan',
    ];

    protected $casts = [
        'jumlah_pakan' => 'float',
        'tanggal_pemberian' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Accessor for formatted jumlah
     */
    public function getJumlahFormattedAttribute(): string
    {
        return number_format($this->jumlah_pakan, 2) . ' kg';
    }

    /**
     * Accessor for formatted tanggal
     */
    public function getTanggalFormattedAttribute(): string
    {
        return $this->tanggal_pemberian->format('d M Y H:i');
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the ternak that owns the pakan record.
     */
    public function ternak()
    {
        return $this->belongsTo(Ternak::class, 'ternak_id');
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('tanggal_pemberian', 'desc');
    }

    /**
     * Scope a query to filter by jenis pakan.
     */
    public function scopeOfJenis(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis_pakan', $jenis);
    }

    /**
     * Scope a query for a specific ternak.
     */
    public function scopeForTernak(Builder $query, $ternakId): Builder
    {
        return $query->where('ternak_id', $ternakId);
    }

    /**
     * Scope a query to filter by date.
     */
    public function scopeOnDate(Builder $query, $date): Builder
    {
        return $query->whereDate('tanggal_pemberian', $date);
    }
}