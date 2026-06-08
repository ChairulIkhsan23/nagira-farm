<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TernakResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'slug' => $this->slug,
            'kode_ternak' => $this->kode_ternak,
            'nama_ternak' => $this->nama_ternak,
            'jenis_ternak' => $this->jenis_ternak,
            'kategori' => $this->kategori,
            'jenis_kelamin' => $this->jenis_kelamin,
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'umur' => $this->tanggal_lahir ? now()->diffInMonths($this->tanggal_lahir) . ' bulan' : null,
            'bobot' => $this->bobot,
            'foto' => $this->foto ? asset('storage/' . $this->foto) : null,
            'status_aktif' => $this->status_aktif,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];

        if ($request->boolean('include_price_range') || (bool) ($this->include_price_range ?? false)) {
            $data['price_range'] = $this->estimated_price_range;
        }

        return $data;
    }
}
