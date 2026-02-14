<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class KategoriArtikel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kategori_artikels';

    protected $fillable = [
        'slug',
        'nama_kategori',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($kategori) {
            if (empty($kategori->slug)) {
                $kategori->slug = str()->slug($kategori->nama_kategori);
            }
        });

        static::updating(function ($kategori) {
            if ($kategori->isDirty('nama_kategori')) {
                $kategori->slug = str()->slug($kategori->nama_kategori);
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
     * Accessor for published artikels count.
     */
    public function getPublishedArtikelsCountAttribute(): int
    {
        return $this->artikels()->where('status', 'published')->count();
    }

    /**
     * Accessor for total artikels count.
     */
    public function getTotalArtikelsCountAttribute(): int
    {
        return $this->artikels()->count();
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the artikels for this kategori.
     */
    public function artikels()
    {
        return $this->hasMany(Artikel::class, 'kategori_id');
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to search by name.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('nama_kategori', 'LIKE', "%{$term}%");
    }

    /**
     * Scope a query to order by most artikels.
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount('artikels')->orderBy('artikels_count', 'desc');
    }
}