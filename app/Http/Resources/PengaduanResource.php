<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengaduanResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_pengirim' => $this->nama_pengirim,
            'kategori' => $this->kategori,
            'subjek' => $this->subjek,
            'pesan' => $this->pesan,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
