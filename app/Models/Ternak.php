<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    'bobot',   
    'foto',
    'status_aktif',
    'induk_id',
    'pejantan_id',
    'berat_lahir',
];
   


    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_timbang_terakhir' => 'date',
        'bobot' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
    'status_aktif' => 'aktif',
    'bobot' => 0,  
];
    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ternak) {
            
            if (empty($ternak->kode_ternak)) {
                $ternak->kode_ternak = static::generateKodeTernak($ternak->jenis_ternak);
            }
            
            
            if (empty($ternak->slug)) {
                $ternak->slug = static::generateSlug($ternak->kode_ternak, $ternak->nama_ternak);
            }
        });

        static::updating(function ($ternak) {
            
            if ($ternak->isDirty('kode_ternak') || $ternak->isDirty('nama_ternak')) {
                $ternak->slug = static::generateSlug($ternak->kode_ternak, $ternak->nama_ternak);
            }
        });
    }

    public function kelahirans()
    {
        return $this->hasMany(Kelahiran::class, 'betina_id');
    }
    public function perkawinansSebagaiBetina()
    {
        return $this->hasMany(Perkawinan::class, 'betina_id');
    }

    public function perkawinansSebagaiPejantan()
    {
        return $this->hasMany(Perkawinan::class, 'pejantan_id');
    }

    
    public static function generateKodeTernak($jenisTernak = null): string
    {
        $prefix = 'TRN';
        
        
        if ($jenisTernak) {
            
            $withoutSpace = str_replace(' ', '', $jenisTernak);
            $prefix = strtoupper(substr($withoutSpace, 0, 5)); 
            
            
            $words = explode(' ', $jenisTernak);
            if (count($words) >= 2) {
                $prefix = '';
                foreach ($words as $word) {
                    $prefix .= strtoupper(substr($word, 0, 2)); 
                }
            }
        }

        
        $lastTernak = static::where('kode_ternak', 'like', $prefix . '%')
            ->orderBy('kode_ternak', 'desc')
            ->first();

        if ($lastTernak) {
            
            $lastNumber = intval(substr($lastTernak->kode_ternak, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }

    
    public static function generateSlug($kodeTernak, $namaTernak = null): string
    {
        $slug = $kodeTernak;
        if ($namaTernak) {
            $slug .= '-' . $namaTernak;
        }
        return str()->slug($slug);
    }

    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    
    public function getFotoUrlAttribute(): ?string
    {
        if (!$this->foto) {
            return null;
        }
        
        
        if (filter_var($this->foto, FILTER_VALIDATE_URL)) {
            return $this->foto;
        }
        
        
        return asset('storage/' . $this->foto);
    }

    
    public function getUmurBulanAttribute(): ?int
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return Carbon::parse($this->tanggal_lahir)->diffInMonths(now());
    }

    
    public function getUmurTahunAttribute(): ?float
    {
        if (!$this->tanggal_lahir) {
            return null;
        }
        return round(Carbon::parse($this->tanggal_lahir)->diffInYears(now()), 1);
    }

    
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

    
    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status_aktif) {
            'aktif' => 'success',
            'mati' => 'danger',
            'terjual' => 'warning',
            default => 'gray',
        };
    }

    
    public function getJenisKelaminIconAttribute(): string
    {
        return match($this->jenis_kelamin) {
            'jantan' => '',
            'betina' => '',
            default => '',
        };
    }

    
    public function getKategoriLabelAttribute(): string
    {
        return match($this->kategori) {
            'regular' => 'Reguler',
            'breeding' => 'Indukan',
            'fattening' => 'Penggemukan',
            default => '-',
        };
    }

    
    public function getKategoriBadgeColorAttribute(): string
    {
        return match($this->kategori) {
            'regular' => 'gray',
            'breeding' => 'warning',
            'fattening' => 'info',
            default => 'gray',
        };
    }

    

    
    public function fattening()
    {
        return $this->hasOne(Fattening::class, 'ternak_id');
    }

    
    public function programFattening(): HasMany
    {
        return $this->hasMany(Fattening::class, 'ternak_id');
    }

    
    public function programFatteningAktif(): HasOne
    {
        return $this->hasOne(Fattening::class, 'ternak_id')
            ->where('status', 'progres');
    }

    
    public function riwayatTimbangs()
    {
        return $this->hasMany(RiwayatTimbang::class, 'ternak_id');
    }

    
    public function kesehatans()
    {
        return $this->hasMany(Kesehatan::class, 'ternak_id');
    }

    
    public function pakans()
    {
        return $this->hasMany(Pakan::class, 'ternak_id');
    }

    
    public function perkawinanSebagaiBetina()
    {
        return $this->hasMany(Perkawinan::class, 'betina_id');
    }

    
    public function perkawinanSebagaiPejantan()
    {
        return $this->hasMany(Perkawinan::class, 'pejantan_id');
    }

    
    public function kelahiranSebagaiBetina()
    {
        return $this->hasMany(Kelahiran::class, 'betina_id');
    }

    
    public function latestKesehatan()
    {
        return $this->hasOne(Kesehatan::class, 'ternak_id')->latest('tanggal_periksa');
    }

    
    public function latestTimbangan()
    {
        return $this->hasOne(RiwayatTimbang::class, 'ternak_id')->latest('tanggal_timbang');
    }

    
    public function getLatestBobotAttribute(): ?float
    {
        $latest = $this->latestTimbangan;
        return $latest?->bobot;
    }

    
    public function getTotalAnakAttribute(): int
    {
        if ($this->jenis_kelamin !== 'betina') {
            return 0;
        }
        
        return $this->kelahiranSebagaiBetina()->sum('jumlah_anak_lahir');
    }

    

    public function induk()
    {
        return $this->belongsTo(Ternak::class, 'induk_id');
    }

    public function pejantan()
    {
        return $this->belongsTo(Ternak::class, 'pejantan_id');
    }

    public function anakDariInduk()
    {
        return $this->hasMany(Ternak::class, 'induk_id');
    }

    public function anakDariPejantan()
    {
        return $this->hasMany(Ternak::class, 'pejantan_id');
    }

    

    
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_aktif', 'aktif');
    }

    public function scopeNonAktif(Builder $query): Builder
    {
        return $query->whereIn('status_aktif', ['mati', 'terjual']);
    }

    public function scopeJantan(Builder $query): Builder
    {
        return $query->where('jenis_kelamin', 'jantan');
    }

    public function scopeBetina(Builder $query): Builder
    {
        return $query->where('jenis_kelamin', 'betina');
    }

    
    public function scopeBreeding(Builder $query): Builder
    {
        return $query->where('kategori', 'breeding');
    }

    
    public function scopeFattening(Builder $query): Builder
    {
        return $query->where('kategori', 'fattening');
    }

    
    public function scopeRegular(Builder $query): Builder
    {
        return $query->where('kategori', 'regular');
    }

    
    public function scopeJenisTernak(Builder $query, string $jenis): Builder
    {
        return $query->where('jenis_ternak', $jenis);
    }

    
    public function scopeUmurMin(Builder $query, int $bulan): Builder
    {
        return $query->where('tanggal_lahir', '<=', now()->subMonths($bulan));
    }

    
    public function scopeUmurMax(Builder $query, int $bulan): Builder
    {
        return $query->where('tanggal_lahir', '>=', now()->subMonths($bulan));
    }

    
    public function scopeUmurRange(Builder $query, int $minBulan, int $maxBulan): Builder
    {
        return $query->whereBetween('tanggal_lahir', [
            now()->subMonths($maxBulan),
            now()->subMonths($minBulan)
        ]);
    }

    
    public function scopeSearch(Builder $query, string $keyword): Builder
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('kode_ternak', 'LIKE', "%{$keyword}%")
            ->orWhere('nama_ternak', 'LIKE', "%{$keyword}%")
            ->orWhere('jenis_ternak', 'LIKE', "%{$keyword}%");
        });
    }

    

    


public function toApiResponse(): array
{
    
    $bobotValue = $this->bobot ?? 0;
    
    
    if ($bobotValue == 0 && $this->latestTimbangan) {
        $bobotValue = $this->latestTimbangan->bobot;
    }
    
    
    if ($bobotValue == 0 && $this->fattening && $this->fattening->bobot_terakhir) {
        $bobotValue = $this->fattening->bobot_terakhir;
    }
    
    
    $kelahirans = [];
    if ($this->kelahirans && $this->kelahirans->count() > 0) {
        foreach ($this->kelahirans as $kelahiran) {
            $kelahirans[] = [
                'id' => $kelahiran->id,
                'betina_id' => $kelahiran->betina_id,
                'perkawinan_id' => $kelahiran->perkawinan_id,
                'tanggal_melahirkan' => $kelahiran->tanggal_melahirkan?->format('Y-m-d'),
                'tanggal_sapih' => $kelahiran->tanggal_sapih?->format('Y-m-d'),
                'umur_sapih_hari' => $kelahiran->umur_sapih_hari,
                'jumlah_anak_lahir' => $kelahiran->jumlah_anak_lahir,
                'jumlah_anak_hidup' => $kelahiran->jumlah_anak_hidup,
                'jumlah_anak_mati' => $kelahiran->jumlah_anak_mati,
                'keterangan' => $kelahiran->keterangan,
                'detail_anak' => $kelahiran->detail_anak ?? [],
                'created_at' => $kelahiran->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $kelahiran->updated_at?->format('Y-m-d H:i:s'),
            ];
        }
    }
    
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
        'bobot' => $bobotValue,
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
        'kelahirans' => $kelahirans, 
        'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
    ];
}

public function toPublicApiResponse(): array
{
    $response = $this->toApiResponse();
    $response['data_kategori'] = null;
    unset($response['kelahirans']);

    return $response;
}

public function getEstimatedPriceRangeAttribute(): array
{
    return static::priceRangeByJenis($this->jenis_ternak);
}

public static function priceRangeByJenis(?string $jenisTernak): array
{
    [$minimum, $maximum] = match ($jenisTernak) {
        'Kambing Boer' => [4500000, 6500000],
        'Kambing Etawa' => [3500000, 5500000],
        'Kambing Peranakan Etawa' => [3200000, 5000000],
        'Kambing Jawarandu' => [2800000, 4200000],
        'Kambing Kacang' => [1800000, 3000000],
        'Kambing Saanen' => [5000000, 7000000],
        'Kambing Alpine' => [4200000, 6200000],
        'Kambing Toggenburg' => [4300000, 6300000],
        'Kambing Anglo Nubian' => [4800000, 6800000],
        'Kambing Kiko' => [4000000, 6000000],
        'Kambing Myotonic (Fainting Goat)' => [4500000, 6500000],
        'Kambing LaMancha' => [4200000, 6200000],
        'Kambing Oberhasli' => [4100000, 6100000],
        default => [2500000, 4500000],
    };

    return [
        'min' => $minimum,
        'max' => $maximum,
        'label' => sprintf(
            'Rp%s - Rp%s',
            number_format($minimum, 0, ',', '.'),
            number_format($maximum, 0, ',', '.'),
        ),
    ];
}
}
