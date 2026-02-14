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
        'isi',
        'status',
        'tanggal_publish',
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

    /**
     * Boot the model.
     */
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
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Accessor for excerpt.
     */
    public function getExcerptAttribute(int $length = 200): string
    {
        return str()->limit(strip_tags($this->isi), $length);
    }

    /**
     * Accessor for reading time.
     */
    public function getReadingTimeAttribute(): string
    {
        $words = str_word_count(strip_tags($this->isi));
        $minutes = ceil($words / 200); // Average reading speed: 200 words/minute
        
        return $minutes . ' menit';
    }

    /**
     * Accessor for status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return $this->status === 'published' ? 'success' : 'warning';
    }

    /**
     * Accessor for status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'published' ? 'Dipublikasi' : 'Draft';
    }

    /**
     * Check if artikel is published.
     */
    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'published' 
            && $this->tanggal_publish 
            && $this->tanggal_publish->lte(now());
    }

    /**
     * Accessor for formatted tanggal publish.
     */
    public function getTanggalPublishFormattedAttribute(): string
    {
        return $this->tanggal_publish ? $this->tanggal_publish->format('d M Y H:i') : '-';
    }

    /**
     * Accessor for featured image URL.
     */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    // ===================== RELATIONSHIPS =====================

    /**
     * Get the kategori that owns the artikel.
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriArtikel::class, 'kategori_id');
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to only include published artikels.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('tanggal_publish', '<=', now());
    }

    /**
     * Scope a query to only include draft artikels.
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to order by latest published.
     */
    public function scopeLatestPublished(Builder $query): Builder
    {
        return $query->published()->orderBy('tanggal_publish', 'desc');
    }

    /**
     * Scope a query to filter by kategori.
     */
    public function scopeOfKategori(Builder $query, $kategoriId): Builder
    {
        return $query->where('kategori_id', $kategoriId);
    }

    /**
     * Scope a query to search by title or content.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('judul', 'LIKE', "%{$term}%")
            ->orWhere('isi', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopePublishedBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('tanggal_publish', [$startDate, $endDate]);
    }
}