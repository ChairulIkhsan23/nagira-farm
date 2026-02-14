<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Kelahiran extends Model
{
    use HasFactory;

    protected $table = 'kelahirans';

    protected $fillable = [
        'betina_id',
        'perkawinan_id',
        'tanggal_melahirkan',
        'jumlah_anak_lahir',
        'jumlah_anak_hidup',
        'jumlah_anak_mati',
        'keterangan',
        'detail_anak',
    ];

    protected $casts = [
        'tanggal_melahirkan' => 'date',
        'jumlah_anak_lahir' => 'integer',
        'jumlah_anak_hidup' => 'integer',
        'jumlah_anak_mati' => 'integer',
        'detail_anak' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kelahiran) {
            // Auto calculate jumlah anak lahir dari hidup + mati jika tidak diisi
            if (!$kelahiran->jumlah_anak_lahir && ($kelahiran->jumlah_anak_hidup || $kelahiran->jumlah_anak_mati)) {
                $kelahiran->jumlah_anak_lahir = ($kelahiran->jumlah_anak_hidup ?? 0) + ($kelahiran->jumlah_anak_mati ?? 0);
            }
        });

        static::created(function ($kelahiran) {
            // Update status perkawinan menjadi 'melahirkan'
            if ($kelahiran->perkawinan_id) {
                $kelahiran->perkawinan()->update(['status_siklus' => 'melahirkan']);
            }
        });
    }

    /**
     * Accessor for mortalitas rate
     */
    public function getMortalitasRateAttribute(): float
    {
        if ($this->jumlah_anak_lahir <= 0) {
            return 0;
        }
        
        return round(($this->jumlah_anak_mati / $this->jumlah_anak_lahir) * 100, 1);
    }

    /**
     * Accessor for survival rate
     */
    public function getSurvivalRateAttribute(): float
    {
        if ($this->jumlah_anak_lahir <= 0) {
            return 0;
        }
        
        return round(($this->jumlah_anak_hidup / $this->jumlah_anak_lahir) * 100, 1);
    }

    /**
     * Accessor for jenis kelamin summary dari detail_anak
     */
    public function getJenisKelaminSummaryAttribute(): array
    {
        if (!$this->detail_anak) {
            return ['jantan' => 0, 'betina' => 0, 'unknown' => 0];
        }
        
        $summary = ['jantan' => 0, 'betina' => 0, 'unknown' => 0];
        
        foreach ($this->detail_anak as $anak) {
            $jk = $anak['jenis_kelamin'] ?? 'unknown';
            if (in_array($jk, ['jantan', 'betina'])) {
                $summary[$jk]++;
            } else {
                $summary['unknown']++;
            }
        }
        
        return $summary;
    }

    /**
     * Accessor for formatted tanggal
     */
    public function getTanggalFormattedAttribute(): string
    {
        return $this->tanggal_melahirkan->format('d M Y');
    }

    /**
     * Get children details as collection
     */
    public function getAnakCollectionAttribute()
    {
        return collect($this->detail_anak);
    }

    /**
     * Get jumlah anak jantan
     */
    public function getJumlahAnakJantanAttribute(): int
    {
        return $this->jenis_kelamin_summary['jantan'];
    }

    /**
     * Get jumlah anak betina
     */
    public function getJumlahAnakBetinaAttribute(): int
    {
        return $this->jenis_kelamin_summary['betina'];
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the betina (mother) ternak.
     */
    public function betina()
    {
        return $this->belongsTo(Ternak::class, 'betina_id');
    }

    /**
     * Get the perkawinan that led to this kelahiran.
     */
    public function perkawinan()
    {
        return $this->belongsTo(Perkawinan::class, 'perkawinan_id');
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('tanggal_melahirkan', [$startDate, $endDate]);
    }

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('tanggal_melahirkan', 'desc');
    }

    /**
     * Scope a query for a specific betina.
     */
    public function scopeForBetina(Builder $query, $betinaId): Builder
    {
        return $query->where('betina_id', $betinaId);
    }

    /**
     * Scope a query for kelahiran with high mortality.
     */
    public function scopeHighMortality(Builder $query, float $threshold = 50): Builder
    {
        return $query->whereRaw('(jumlah_anak_mati * 100.0 / jumlah_anak_lahir) >= ?', [$threshold]);
    }
}