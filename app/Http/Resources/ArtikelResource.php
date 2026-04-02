<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtikelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'judul' => $this->judul,
            'excerpt' => $this->excerpt,
            'foto' => $this->foto ? asset('storage/' . $this->foto) : null,
            'og_image' => $this->og_image ? asset('storage/' . $this->og_image) : null,
            'isi' => $request->routeIs('api.v1.artikel.show') ? $this->isi : null, // Hanya tampilkan isi lengkap di detail
            'status' => $this->status,
            'views' => $this->views,
            'is_featured' => $this->is_featured,
            'tanggal_publish' => $this->tanggal_publish?->format('Y-m-d H:i:s'),
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'kategori' => $this->whenLoaded('kategori', function() {
                return [
                    'id' => $this->kategori->id,
                    'slug' => $this->kategori->slug,
                    'nama_kategori' => $this->kategori->nama_kategori,
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
