<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatTimbang extends Model
{
    use HasFactory;

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
    ];

    protected static function booted(): void
    {
        static::created(function (RiwayatTimbang $timbang) {
            // UPDATE FATTENING
            if ($timbang->fattening_id) {
                $fattening = $timbang->fattening;            
                $fattening->bobot_terakhir = $timbang->bobot;
                $fattening->save();                          
            }
            
            // TAMBAHKAN INI - UPDATE BOBOT TERNAK
            if ($timbang->ternak_id) {
                $ternak = $timbang->ternak;
                $ternak->bobot = $timbang->bobot;
                $ternak->tanggal_timbang_terakhir = $timbang->tanggal_timbang;
                $ternak->save();
            }
        });

        static::updated(function (RiwayatTimbang $timbang) {
            if ($timbang->isDirty('bobot')) {
                // UPDATE FATTENING
                if ($timbang->fattening_id) {
                    $fattening = $timbang->fattening;             
                    $fattening->bobot_terakhir = $timbang->bobot;
                    $fattening->save();                           
                }
                
                // TAMBAHKAN INI - UPDATE BOBOT TERNAK
                if ($timbang->ternak_id) {
                    $ternak = $timbang->ternak;
                    $ternak->bobot = $timbang->bobot;
                    $ternak->tanggal_timbang_terakhir = $timbang->tanggal_timbang;
                    $ternak->save();
                }
            }
        });

        static::deleted(function (RiwayatTimbang $timbang) {
            // UPDATE FATTENING
            if ($timbang->fattening_id) {
                $fattening = $timbang->fattening;             
                
                $riwayatTerakhir = $fattening->riwayatTimbangs()
                    ->where('id', '!=', $timbang->id)
                    ->latest('tanggal_timbang')
                    ->first();
                
                if ($riwayatTerakhir) {
                    $fattening->bobot_terakhir = $riwayatTerakhir->bobot;
                } else {
                    $fattening->bobot_terakhir = $fattening->bobot_awal;
                }
                
                $fattening->save();                            
            }
            
            // TAMBAHKAN INI - UPDATE BOBOT TERNAK
            if ($timbang->ternak_id) {
                $ternak = $timbang->ternak;
                $riwayatTerakhirTernak = $ternak->riwayatTimbangs()
                    ->where('id', '!=', $timbang->id)
                    ->latest('tanggal_timbang')
                    ->first();
                
                if ($riwayatTerakhirTernak) {
                    $ternak->bobot = $riwayatTerakhirTernak->bobot;
                    $ternak->tanggal_timbang_terakhir = $riwayatTerakhirTernak->tanggal_timbang;
                }
                
                $ternak->save();
            }
        });
    }

    // ===================== ACCESSORS =====================

    public function getBobotFormattedAttribute(): string
    {
        return number_format($this->bobot, 1) . ' kg';
    }

    public function getTanggalFormattedAttribute(): string
    {
        return $this->tanggal_timbang->format('d M Y H:i');
    }

    public function getTanggalShortAttribute(): string
    {
        return $this->tanggal_timbang->format('d M Y');
    }

    public function getSelisihBobotAttribute(): ?float
    {
        $sebelumnya = $this->fattening
            ?->riwayatTimbangs()
            ->where('tanggal_timbang', '<', $this->tanggal_timbang)
            ->latest('tanggal_timbang')
            ->first();

        return $sebelumnya ? round($this->bobot - $sebelumnya->bobot, 1) : null;
    }

    public function getSelisihBobotFormattedAttribute(): ?string
    {
        $selisih = $this->selisih_bobot;
        
        if ($selisih === null) {
            return '-';
        }
        
        $prefix = $selisih > 0 ? '+' : '';
        return $prefix . number_format($selisih, 1) . ' kg';
    }

    public function getSelisihHariAttribute(): ?int
    {
        $sebelumnya = $this->fattening
            ?->riwayatTimbangs()
            ->where('tanggal_timbang', '<', $this->tanggal_timbang)
            ->latest('tanggal_timbang')
            ->first();

        return $sebelumnya ? $sebelumnya->tanggal_timbang->diffInDays($this->tanggal_timbang) : null;
    }

    public function getAdgAttribute(): ?float
    {
        $selisihBobot = $this->selisih_bobot;
        $selisihHari = $this->selisih_hari;

        if ($selisihBobot === null || $selisihHari === null || $selisihHari == 0) {
            return null;
        }

        return round($selisihBobot / $selisihHari, 2);
    }

    public function getAdgFormattedAttribute(): ?string
    {
        $adg = $this->adg;
        return $adg ? number_format($adg, 2) . ' kg/hari' : '-';
    }

    public function getProgressToTargetAttribute(): ?float
    {
        return $this->fattening?->progress_persen;
    }

    public function getIsFirstWeighingAttribute(): bool
    {
        if (!$this->fattening) return false;
        
        $first = $this->fattening->riwayatTimbangs()
            ->oldest('tanggal_timbang')
            ->first();
            
        return $first && $first->id === $this->id;
    }

    public function getGainColorAttribute(): string
    {
        $selisih = $this->selisih_bobot;
        
        return match(true) {
            $selisih === null => 'gray',
            $selisih > 0 => 'success',
            $selisih < 0 => 'danger',
            default => 'warning',
        };
    }

    // ===================== RELATIONSHIPS =====================

    public function ternak(): BelongsTo
    {
        return $this->belongsTo(Ternak::class, 'ternak_id');
    }

    public function fattening(): BelongsTo
    {
        return $this->belongsTo(Fattening::class, 'fattening_id');
    }

    // ===================== SCOPES =====================

    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('tanggal_timbang', 'desc');
    }

    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy('tanggal_timbang', 'asc');
    }

    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('tanggal_timbang', [$startDate, $endDate]);
    }

    public function scopeForTernak(Builder $query, $ternakId): Builder
    {
        return $query->where('ternak_id', $ternakId);
    }

    public function scopeForFattening(Builder $query, $fatteningId): Builder
    {
        return $query->where('fattening_id', $fatteningId);
    }

    public function scopeHariIni(Builder $query): Builder
    {
        return $query->whereDate('tanggal_timbang', now());
    }

    public function scopeMingguIni(Builder $query): Builder
    {
        return $query->whereBetween('tanggal_timbang', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeBulanIni(Builder $query): Builder
    {
        return $query->whereMonth('tanggal_timbang', now()->month)
            ->whereYear('tanggal_timbang', now()->year);
    }

    // ===================== UTILITY METHODS =====================

    public function getPreviousWeighing(): ?RiwayatTimbang
    {
        return $this->fattening?->riwayatTimbangs()
            ->where('tanggal_timbang', '<', $this->tanggal_timbang)
            ->latest('tanggal_timbang')
            ->first();
    }

    public function getNextWeighing(): ?RiwayatTimbang
    {
        return $this->fattening?->riwayatTimbangs()
            ->where('tanggal_timbang', '>', $this->tanggal_timbang)
            ->oldest('tanggal_timbang')
            ->first();
    }
}