<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Artikel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'artikels';

    protected $fillable = [
        'slug',
        'kategori_id',
        'judul',
        'foto',
        'og_image',
        'isi',
        'excerpt',
        'status',
        'tanggal_publish',
        'is_featured',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'tanggal_publish' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'draft',
    ];

    
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($artikel) {
            if (empty($artikel->slug)) {
                $artikel->slug = str()->slug($artikel->judul);
            }
            
            if ($artikel->status === 'published' && empty($artikel->tanggal_publish)) {
                $artikel->tanggal_publish = now();
            }
        });

        static::updating(function ($artikel) {
            if ($artikel->isDirty('judul')) {
                $artikel->slug = str()->slug($artikel->judul);
            }
            
            if ($artikel->isDirty('status') && $artikel->status === 'published' && empty($artikel->tanggal_publish)) {
                $artikel->tanggal_publish = now();
            }
        });

        static::saving(function ($artikel) {
            if (empty($artikel->getRawOriginal('excerpt')) && !empty($artikel->isi)) {
                $artikel->excerpt = str()->limit(strip_tags($artikel->isi), 200);
            }
        });
    }

    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    
    public function getExcerptAttribute($value): ?string
    {
        if (!empty($value)) {
            return $value;
        }

        if (!empty($this->isi)) {
            return str()->limit(strip_tags($this->isi), 200);
        }
        return null;
    }

    
    public function getReadingTimeAttribute(): string
    {
        $words = str_word_count(strip_tags($this->isi));
        $minutes = ceil($words / 200); 
        
        return $minutes . ' menit';
    }

    
    public function getStatusBadgeColorAttribute(): string
    {
        return $this->status === 'published' ? 'success' : 'warning';
    }

    
    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'published' ? 'Dipublikasi' : 'Draft';
    }

    
    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published' 
            && $this->tanggal_publish 
            && $this->tanggal_publish->lte(now());
    }

    
    public function getTanggalPublishFormattedAttribute(): string
    {
        return $this->tanggal_publish ? $this->tanggal_publish->format('d M Y H:i') : '-';
    }

    
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    

    
    public function kategori()
    {
        return $this->belongsTo(KategoriArtikel::class, 'kategori_id');
    }

    
    public function komentars()
    {
        return $this->hasMany(Komentars::class)
            ->whereNull('parent_id');
            
    }

    

    
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('tanggal_publish', '<=', now());
    }

    
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    
    public function scopeLatestPublished(Builder $query): Builder
    {
        return $query->published()->orderBy('tanggal_publish', 'desc');
    }

    
    public function scopeOfKategori(Builder $query, $kategoriId): Builder
    {
        return $query->where('kategori_id', $kategoriId);
    }

    
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('judul', 'LIKE', "%{$term}%")
            ->orWhere('isi', 'LIKE', "%{$term}%");
        });
    }

    
    public function scopePublishedBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('tanggal_publish', [$startDate, $endDate]);
    }
}