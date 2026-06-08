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

    
    public function getKondisiBadgeColorAttribute(): string
    {
        return match($this->kondisi) {
            'sehat' => 'success',
            'sakit' => 'warning',
            'kritis' => 'danger',
            default => 'gray',
        };
    }

    
    public function getKondisiLabelAttribute(): string
    {
        return match($this->kondisi) {
            'sehat' => 'Sehat',
            'sakit' => 'Sakit',
            'kritis' => 'Kritis',
            default => 'Unknown',
        };
    }

    
    public function getTanggalPeriksaFormattedAttribute(): string
    {
        return $this->tanggal_periksa ? $this->tanggal_periksa->format('d M Y') : '-';
    }

    

    
    public function ternak()
    {
        return $this->belongsTo(Ternak::class, 'ternak_id');
    }

    

    
    public function scopeSehat(Builder $query): Builder
    {
        return $query->where('kondisi', 'sehat');
    }

    
    public function scopeSakit(Builder $query): Builder
    {
        return $query->where('kondisi', 'sakit');
    }

    
    public function scopeKritis(Builder $query): Builder
    {
        return $query->where('kondisi', 'kritis');
    }

    
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('tanggal_periksa', 'desc');
    }

    
    public function scopeForTernak(Builder $query, $ternakId): Builder
    {
        return $query->where('ternak_id', $ternakId);
    }
}