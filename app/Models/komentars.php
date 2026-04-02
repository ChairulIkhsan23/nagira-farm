<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class komentars extends Model
{
    protected $fillable = [
        'artikel_id',
        'nama',
        'email',
        'isi',
        'parent_id',
        'is_approved',
    ];

    public function artikel()
    {
        return $this->belongsTo(Artikel::class);
    }

    public function parent()
    {
        return $this->belongsTo(Komentars::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Komentars::class, 'parent_id');
    }
}
