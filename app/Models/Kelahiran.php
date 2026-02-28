<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Ternak;
use Illuminate\Support\Str;


class Kelahiran extends Model
{
    use HasFactory;

    protected $table = 'kelahirans';

    protected $fillable = [
        'betina_id',
        'perkawinan_id',
        'tanggal_melahirkan',
        'tanggal_sapih',
        'umur_sapih_hari',
        'jumlah_anak_lahir',
        'jumlah_anak_hidup',
        'jumlah_anak_mati',
        'keterangan',
        'detail_anak',
    ];

    protected $casts = [
        'tanggal_melahirkan' => 'date',
        'tanggal_sapih' => 'date',
        'umur_sapih_hari' => 'integer',
        'jumlah_anak_lahir' => 'integer',
        'jumlah_anak_hidup' => 'integer',
        'jumlah_anak_mati' => 'integer',
        'detail_anak' => 'array',
    ];

    // ================= BOOT ================= //

    protected static function booted()
    {
        static::creating(function ($kelahiran) {
            if (!$kelahiran->jumlah_anak_lahir) {
                $kelahiran->jumlah_anak_lahir =
                    ($kelahiran->jumlah_anak_hidup ?? 0) +
                    ($kelahiran->jumlah_anak_mati ?? 0);
            }
        });

        static::created(function ($kelahiran) {

            if (!$kelahiran->detail_anak || !$kelahiran->betina_id) {
                return;
            }

            $perkawinan = $kelahiran->perkawinan;
            $pejantanId = $perkawinan?->pejantan_id;

            foreach ($kelahiran->detail_anak as $anak) {

                if (empty($anak['jenis_kelamin'])) {
                    continue;
                }

                Ternak::create([
                    'slug'          => Str::uuid(),
                    'kode_ternak'   => null, // auto generate dari model Ternak
                    'nama_ternak'   => $anak['nama_ternak'] ?? null,
                    'jenis_ternak'  => $kelahiran->betina?->jenis_ternak,
                    'kategori'      => $anak['kategori'] ?? 'regular',
                    'jenis_kelamin' => $anak['jenis_kelamin'],
                    'tanggal_lahir' => $kelahiran->tanggal_melahirkan,
                    'status_aktif'  => $anak['status_aktif'] ?? 'aktif',
                    'induk_id'      => $kelahiran->betina_id,
                    'pejantan_id'   => $pejantanId,
                    'berat_lahir'   => $anak['berat_lahir'] ?? null,
                ]);
            }
        });

        static::saving(function ($kelahiran) {
            $kelahiran->umur_sapih_hari ??= 90;

            if ($kelahiran->tanggal_melahirkan) {
                $kelahiran->tanggal_sapih = Carbon::parse($kelahiran->tanggal_melahirkan)
                    ->addDays($kelahiran->umur_sapih_hari);
            }
        });
    }

    // ================= ACCESSORS ================= //

    public function getMortalitasRateAttribute(): float
    {
        if (($this->jumlah_anak_lahir ?? 0) == 0) return 0;
        return round(($this->jumlah_anak_mati / $this->jumlah_anak_lahir) * 100, 1);
    }

    public function getSurvivalRateAttribute(): float
    {
        if (($this->jumlah_anak_lahir ?? 0) == 0) return 0;
        return round(($this->jumlah_anak_hidup / $this->jumlah_anak_lahir) * 100, 1);
    }

    public function getJenisKelaminSummaryAttribute(): array
    {
        $summary = ['jantan' => 0, 'betina' => 0, 'unknown' => 0];

        foreach ($this->detail_anak ?? [] as $anak) {
            $jk = $anak['jenis_kelamin'] ?? 'unknown';
            $summary[$jk] = ($summary[$jk] ?? 0) + 1;
        }

        return $summary;
    }

    public function getTanggalFormattedAttribute(): ?string
    {
        return $this->tanggal_melahirkan?->format('d M Y');
    }

    public function getSisaSapihHariAttribute(): int
    {
        return $this->tanggal_sapih
            ? now()->diffInDays($this->tanggal_sapih, false)
            : 0;
    }

    public function getStatusSapihAttribute(): string
    {
        if (!$this->tanggal_sapih) return '-';

        return now()->lt($this->tanggal_sapih)
            ? "Menyusui ({$this->sisa_sapih_hari} hari lagi)"
            : "Sudah Sapih";
    }

    public function getAnakCollectionAttribute()
    {
        return collect($this->detail_anak ?? []);
    }

    public function getJumlahAnakJantanAttribute(): int
    {
        return $this->jenis_kelamin_summary['jantan'];
    }

    public function getJumlahAnakBetinaAttribute(): int
    {
        return $this->jenis_kelamin_summary['betina'];
    }

    // ================= RELATIONSHIPS ================= //

    public function betina()
    {
        return $this->belongsTo(Ternak::class, 'betina_id');
    }

    public function perkawinan()
    {
        return $this->belongsTo(Perkawinan::class, 'perkawinan_id');
    }

    public function induk()
    {
        return $this->belongsTo(Ternak::class, 'induk_id');
    }

    public function pejantan()
    {
        return $this->belongsTo(Ternak::class, 'pejantan_id');
    }

    // ================= SCOPES ================= //

    public function scopeBetweenDates(Builder $query, $start, $end): Builder
    {
        return $query->whereBetween('tanggal_melahirkan', [$start, $end]);
    }

    public function scopeForBetina(Builder $query, $betinaId): Builder
    {
        return $query->where('betina_id', $betinaId);
    }

    public function scopeHighMortality(Builder $query, float $threshold = 50): Builder
    {
        return $query->whereRaw('jumlah_anak_lahir > 0 AND (jumlah_anak_mati * 100.0 / jumlah_anak_lahir) >= ?', [$threshold]);
    }
}