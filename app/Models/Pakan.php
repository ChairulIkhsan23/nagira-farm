<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Pakan extends Model
{
    protected $table = 'pakans';

    protected $fillable = [
        'slug',
        'kode_pakan',
        'jenis_pakan',
        'nama_pakan',
        'stok',
        'satuan',   
        'catatan',
    ];

    protected $casts = [
        'stok' => 'decimal:2',
    ];

    protected static function booted()
    {
        parent::boot();

        static::creating(function ($pakan) {

            // Generate kode otomatis
            if (empty($pakan->kode_pakan)) {
                $pakan->kode_pakan = static::generateKodePakan($pakan->jenis_pakan);
            }

            // Generate slug
            if (empty($pakan->slug)) {
                $pakan->slug = static::generateSlug($pakan->kode_pakan, $pakan->nama_pakan);
            }
        });

        static::updating(function ($pakan) {
            if ($pakan->isDirty('kode_pakan') || $pakan->isDirty('nama_pakan')) {
                $pakan->slug = static::generateSlug($pakan->kode_pakan, $pakan->nama_pakan);
            }
        });
    }

    /**
     * Generate kode pakan otomatis
     */
    public static function generateKodePakan($jenisPakan = null): string
    {
        $prefix = 'PKN';

        if ($jenisPakan) {
            $withoutSpace = str_replace(' ', '', $jenisPakan);
            $prefix = strtoupper(substr($withoutSpace, 0, 4));
        }

        $last = static::where('kode_pakan', 'like', $prefix . '%')
            ->orderBy('kode_pakan', 'desc')
            ->first();

        if ($last) {
            $lastNumber = intval(substr($last->kode_pakan, -3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }

    /**
     * Generate slug
     */
    public static function generateSlug($kode, $nama = null): string
    {
        $slug = $kode;
        if ($nama) {
            $slug .= '-' . $nama;
        }
        return str()->slug($slug);
    }

    /**
     * Route key pakai slug
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Relasi ke pakan_ternak
     */
    public function pakanTernaks()
    {
        return $this->hasMany(PakanTernak::class);
    }

    /**
     * Scope stok habis
     */
    public function scopeStokHabis(Builder $query): Builder
    {
        return $query->where('stok', '<=', 0);
    }

    /**
     * Scope stok menipis
     */
    public function scopeStokMenipis(Builder $query): Builder
    {
        return $query->whereBetween('stok', [0.1, 10]);
    }
}
