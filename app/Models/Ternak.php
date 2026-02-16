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
        'status_aktif' => 'Aktif',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ternak) {
            // Generate kode_ternak otomatis jika kosong
            if (empty($ternak->kode_ternak)) {
                $ternak->kode_ternak = static::generateKodeTernak($ternak->jenis_ternak);
            }
            
            // Generate slug dari kode_ternak dan nama_ternak
            if (empty($ternak->slug)) {
                $ternak->slug = static::generateSlug($ternak->kode_ternak, $ternak->nama_ternak);
            }
        });

        static::updating(function ($ternak) {
            // Update slug jika kode_ternak atau nama_ternak berubah
            if ($ternak->isDirty('kode_ternak') || $ternak->isDirty('nama_ternak')) {
                $ternak->slug = static::generateSlug($ternak->kode_ternak, $ternak->nama_ternak);
            }
        });
    }

    /**
     * Generate kode ternak otomatis
     */
    public static function generateKodeTernak($jenisTernak = null): string
    {
        $prefix = 'TRN';
        
        // Ambil prefix dari jenis ternak
        if ($jenisTernak) {
            // Hapus spasi dan ambil 3-5 huruf pertama
            $withoutSpace = str_replace(' ', '', $jenisTernak);
            $prefix = strtoupper(substr($withoutSpace, 0, 5)); // Ambil 5 huruf pertama
            
            // Alternatif: ambil huruf pertama dari setiap kata
            $words = explode(' ', $jenisTernak);
            if (count($words) >= 2) {
                $prefix = '';
                foreach ($words as $word) {
                    $prefix .= strtoupper(substr($word, 0, 2)); // Ambil 2 huruf pertama tiap kata
                }
            }
        }

        // Cari kode terakhir dengan prefix yang sama
        $lastTernak = static::where('kode_ternak', 'like', $prefix . '%')
            ->orderBy('kode_ternak', 'desc')
            ->first();

        if ($lastTernak) {
            // Extract nomor urut dari kode terakhir (ambil 3 digit terakhir)
            $lastNumber = intval(substr($lastTernak->kode_ternak, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Generate slug
     */
    public static function generateSlug($kodeTernak, $namaTernak = null): string
    {
        $slug = $kodeTernak;
        if ($namaTernak) {
            $slug .= '-' . $namaTernak;
        }
        return str()->slug($slug);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Accessor untuk foto (menghasilkan URL lengkap)
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) {
            return null;
        }
        
        // Jika foto sudah berisi URL lengkap
        if (filter_var($this->foto, FILTER_VALIDATE_URL)) {
            return $this->foto;
        }
        
        // Asumsikan foto disimpan di storage
        return asset('storage/' . $this->foto);
    }

    /**
     * Accessor untuk umur dalam bulan
     */
    public function getUmurBulanAttribute(): ?int
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return Carbon::parse($this->tanggal_lahir)->diffInMonths(now());
    }

    /**
     * Accessor untuk umur dalam tahun
     */
    public function getUmurTahunAttribute(): ?float
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return round(Carbon::parse($this->tanggal_lahir)->diffInYears(now()), 1);
    }

    /**
     * Accessor untuk formatted umur
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
     * Accessor untuk status badge color
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status_aktif) {
            'Aktif' => 'success',
            'Mati' => 'danger',
            'Terjual' => 'warning',
            default => 'gray',
        };
    }

    /**
     * Accessor untuk jenis kelamin icon
     */
    public function getJenisKelaminIconAttribute(): string
    {
        return match($this->jenis_kelamin) {
            'Jantan' => '♂',
            'Betina' => '♀',
            default => '',
        };
    }

    /**
     * Accessor untuk kategori label
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

    /**
     * Accessor untuk kategori badge color
     */
    public function getKategoriBadgeColorAttribute(): string
    {
        return match($this->kategori) {
            'regular' => 'gray',
            'breeding' => 'warning',
            'fattening' => 'info',
            default => 'gray',
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

    /**
     * Get total anak from kelahiran (if betina)
     */
    public function getTotalAnakAttribute(): int
    {
        if ($this->jenis_kelamin !== 'Betina') {
            return 0;
        }
        
        return $this->kelahiranSebagaiBetina()->sum('jumlah_anak');
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to only include aktif ternak.
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_aktif', 'Aktif');
    }

    /**
     * Scope a query to only include non-aktif (mati/terjual) ternak.
     */
    public function scopeNonAktif(Builder $query): Builder
    {
        return $query->whereIn('status_aktif', ['Mati', 'Terjual']);
    }

    /**
     * Scope a query to only include jantan.
     */
    public function scopeJantan(Builder $query): Builder
    {
        return $query->where('jenis_kelamin', 'Jantan');
    }

    /**
     * Scope a query to only include betina.
     */
    public function scopeBetina(Builder $query): Builder
    {
        return $query->where('jenis_kelamin', 'Betina');
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
     * Scope a query to filter by jenis ternak.
     */
    public function scopeJenisTernak(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis_ternak', $jenis);
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

    /**
     * Scope a query to filter by umur range.
     */
    public function scopeUmurRange(Builder $query, int $minBulan, int $maxBulan): Builder
    {
        return $query->whereBetween('tanggal_lahir', [
            now()->subMonths($maxBulan),
            now()->subMonths($minBulan)
        ]);
    }

    /**
     * Scope a query to search by keyword.
     */
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('kode_ternak', 'LIKE', "%{$keyword}%")
            ->orWhere('nama_ternak', 'LIKE', "%{$keyword}%")
            ->orWhere('jenis_ternak', 'LIKE', "%{$keyword}%");
        });
    }

    // ===================== CUSTOM ATTRIBUTES =====================

    /**
     * Get all attributes for API response.
     */
    public function toApiResponse(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'kode_ternak' => $this->kode_ternak,
            'nama_ternak' => $this->nama_ternak,
            'jenis_ternak' => $this->jenis_ternak,
            'kategori' => [
                'value' => $this->kategori,
                'label' => $this->kategori_label,
                'badge_color' => $this->kategori_badge_color,
            ],
            'jenis_kelamin' => [
                'value' => $this->jenis_kelamin,
                'icon' => $this->jenis_kelamin_icon,
            ],
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'umur' => [
                'bulan' => $this->umur_bulan,
                'tahun' => $this->umur_tahun,
                'formatted' => $this->umur_formatted,
            ],
            'foto' => $this->foto_url,
            'status_aktif' => [
                'value' => $this->status_aktif,
                'badge_color' => $this->status_badge_color,
            ],
            'statistik' => [
                'latest_bobot' => $this->latest_bobot,
                'total_anak' => $this->total_anak,
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}