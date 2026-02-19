<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Pengaduan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengaduans';

    protected $fillable = [
        'nama_pengirim',
        'email',
        'kategori',
        'subjek',
        'pesan',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Accessor for formatted created date.
     */
    public function getFormattedCreatedAttribute(): string
    {
        return $this->created_at->format('d M Y H:i');
    }

    /**
     * Accessor for short message.
     */
    public function getPesanSingkatAttribute(int $length = 100): string
    {
        return str()->limit($this->pesan, $length);
    }

    /**
     * Check if has email.
     */
    public function getHasEmailAttribute(): bool
    {
        return !is_null($this->email);
    }

    // ===================== SCOPES =====================

    /**
     * Scope a query to filter by kategori.
     */
    public function scopeOfKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Scope a query to order by latest.
     */
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to search by name or message.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('nama_pengirim', 'LIKE', "%{$term}%")
            ->orWhere('email', 'LIKE', "%{$term}%")
            ->orWhere('subjek', 'LIKE', "%{$term}%")
            ->orWhere('pesan', 'LIKE', "%{$term}%");
        });
    }
}