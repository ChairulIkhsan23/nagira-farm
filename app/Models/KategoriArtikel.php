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

    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    
    public function getPublishedArtikelsCountAttribute(): int
    {
        return $this->artikels()->where('status', 'published')->count();
    }

    
    public function getTotalArtikelsCountAttribute(): int
    {
        return $this->artikels()->count();
    }

    

    
    public function artikels()
    {
        return $this->hasMany(Artikel::class, 'kategori_id');
    }

    

    
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where('nama_kategori', 'LIKE', "%{$term}%");
    }

    
    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount('artikels')->orderBy('artikels_count', 'desc');
    }
}