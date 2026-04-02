<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategoriArtikelResource extends JsonResource
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
            'nama_kategori' => $this->nama_kategori,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'artikel_count' => $this->whenCounted('artikels'),
        ];
    }
}
