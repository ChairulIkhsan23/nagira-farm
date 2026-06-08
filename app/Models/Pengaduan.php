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

    
    public function getFormattedCreatedAttribute(): string
    {
        return $this->created_at->format('d M Y H:i');
    }

    
    public function getPesanSingkatAttribute(int $length = 100): string
    {
        return str()->limit($this->pesan, $length);
    }

    
    public function getHasEmailAttribute(): bool
    {
        return !is_null($this->email);
    }

    

    
    public function scopeOfKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }

    
    public function scopeLatest(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    
    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    
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