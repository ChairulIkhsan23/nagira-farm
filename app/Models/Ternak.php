<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Ternak extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ternaks';

    protected $fillable = [
        'slug',
        'kode_ternak',
        'nama_ternak',
        'jenis_ternak',
        'kategori',
        'jenis_kelamin',
        'tanggal_lahir',
        'foto',
        'status_aktif',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status_aktif' => 'aktif',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ternak) {
            if (empty($ternak->slug)) {
                $ternak->slug = str()->slug($ternak->kode_ternak . '-' . ($ternak->nama_ternak ?? 'ternak'));
            }
        });

        static::updating(function ($ternak) {
            if ($ternak->isDirty('kode_ternak') || $ternak->isDirty('nama_ternak')) {
                $ternak->slug = str()->slug($ternak->kode_ternak . '-' . ($ternak->nama_ternak ?? 'ternak'));
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Accessor for umur dalam bulan
     */
    public function getUmurBulanAttribute(): ?int
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return Carbon::parse($this->tanggal_lahir)->diffInMonths(now());
    }

    /**
     * Accessor for umur dalam tahun
     */
    public function getUmurTahunAttribute(): ?float
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return round(Carbon::parse($this->tanggal_lahir)->diffInYears(now()), 1);
    }

    /**
     * Accessor for formatted umur
     */
    public function getUmurFormattedAttribute(): string
    {
        if (!$this->tanggal_lahir) {
            return '-';
        }
        
        $umurBulan = $this->umur_bulan;
        if ($umurBulan < 24) {
            return $umurBulan . ' bulan';
        }
        
        $tahun = floor($umurBulan / 12);
        $bulan = $umurBulan % 12;
        
        return $bulan > 0 ? $tahun . ' tahun ' . $bulan . ' bulan' : $tahun . ' tahun';
    }

    /**
     * Accessor for status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status_aktif) {
            'aktif' => 'success',
            'mati' => 'danger',
            'terjual' => 'warning',
            default => 'gray',
        };
    }

    /**
     * Accessor for jenis kelamin icon
     */
    public function getJenisKelaminIconAttribute(): string
    {
        return match($this->jenis_kelamin) {
            'jantan' => '♂',
            'betina' => '♀',
            default => '',
        };
    }

    /**
     * Accessor for kategori label
     */
    public function getKategoriLabelAttribute(): string
    {
        return match($this->kategori) {
            'regular' => 'Reguler',
            'breeding' => 'Indukan',
            'fattening' => 'Penggemukan',
            default => '-',
        };
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the fattening record for this ternak.
     */
    public function fattening()
    {
        return $this->hasOne(Fattening::class, 'ternak_id');
    }

    /**
     * Get the riwayat timbangs for this ternak.
     */
    public function riwayatTimbangs()
    {
        return $this->hasMany(RiwayatTimbang::class, 'ternak_id');
    }

    /**
     * Get the kesehatans for this ternak.
     */
    public function kesehatans()
    {
        return $this->hasMany(Kesehatan::class, 'ternak_id');
    }

    /**
     * Get the pakans for this ternak.
     */
    public function pakans()
    {
        return $this->hasMany(Pakan::class, 'ternak_id');
    }

    /**
     * Get the perkawinans where this ternak is betina.
     */
    public function perkawinanSebagaiBetina()
    {
        return $this->hasMany(Perkawinan::class, 'betina_id');
    }

    /**
     * Get the perkawinans where this ternak is pejantan.
     */
    public function perkawinanSebagaiPejantan()
    {
        return $this->hasMany(Perkawinan::class, 'pejantan_id');
    }

    /**
     * Get the kelahirans where this ternak is betina.
     */
    public function kelahiranSebagaiBetina()
    {
        return $this->hasMany(Kelahiran::class, 'betina_id');
    }

    /**
     * Get latest kesehatan record.
     */
    public function latestKesehatan()
    {
        return $this->hasOne(Kesehatan::class, 'ternak_id')->latest('tanggal_periksa');
    }

    /**
     * Get latest timbangan record.
     */
    public function latestTimbangan()
    {
        return $this->hasOne(RiwayatTimbang::class, 'ternak_id')->latest('tanggal_timbang');
    }

    /**
     * Get latest bobot from timbangan.
     */
    public function getLatestBobotAttribute(): ?float
    {
        $latest = $this->latestTimbangan;
        return $latest?->bobot;
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to only include aktif ternak.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_aktif', 'aktif');
    }

    /**
     * Scope a query to only include non-aktif (mati/terjual) ternak.
     */
    public function scopeNonAktif(Builder $query): Builder
    {
        return $query->whereIn('status_aktif', ['mati', 'terjual']);
    }

    /**
     * Scope a query to only include jantan.
     */
    public function scopeJantan(Builder $query): Builder
    {
        return $query->where('jenis_kelamin', 'jantan');
    }

    /**
     * Scope a query to only include betina.
     */
    public function scopeBetina(Builder $query): Builder
    {
        return $query->where('jenis_kelamin', 'betina');
    }

    /**
     * Scope a query to only include breeding category.
     */
    public function scopeBreeding(Builder $query): Builder
    {
        return $query->where('kategori', 'breeding');
    }

    /**
     * Scope a query to only include fattening category.
     */
    public function scopeFattening(Builder $query): Builder
    {
        return $query->where('kategori', 'fattening');
    }

    /**
     * Scope a query to only include regular category.
     */
    public function scopeRegular(Builder $query): Builder
    {
        return $query->where('kategori', 'regular');
    }

    /**
     * Scope a query to filter by umur minimal.
     */
    public function scopeUmurMin(Builder $query, int $bulan): Builder
    {
        return $query->where('tanggal_lahir', '<=', now()->subMonths($bulan));
    }

    /**
     * Scope a query to filter by umur maksimal.
     */
    public function scopeUmurMax(Builder $query, int $bulan): Builder
    {
        return $query->where('tanggal_lahir', '>=', now()->subMonths($bulan));
    }
}