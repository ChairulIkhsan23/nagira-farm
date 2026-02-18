<?php
namespace App\Models;

use App\Models\Pakan;
use Illuminate\Database\Eloquent\Model;

class PakanTernak extends Model
{
    protected $table = 'pakan_ternak';

    protected $fillable = [
        'ternak_id',
        'pakan_id',
        'jumlah',
        'tanggal',
    ];

   protected static function booted()
{
    // =========================
    // CREATE
    // =========================
    static::creating(function ($model) {

        $pakan = Pakan::lockForUpdate()->find($model->pakan_id);

        if (!$pakan) {
            throw new \Exception('Pakan tidak ditemukan.');
        }

        if ($pakan->stok < $model->jumlah) {
            throw new \Exception('Stok tidak mencukupi.');
        }

        $pakan->decrement('stok', $model->jumlah);
    });

    // =========================
    // UPDATE
    // =========================
    static::updating(function ($model) {

        $jumlahLama = $model->getOriginal('jumlah');
        $pakanLamaId = $model->getOriginal('pakan_id');

        $jumlahBaru = $model->jumlah;
        $pakanBaruId = $model->pakan_id;

        // 🔄 Jika ganti pakan
        if ($pakanLamaId != $pakanBaruId) {

            $pakanLama = Pakan::lockForUpdate()->find($pakanLamaId);
            $pakanBaru = Pakan::lockForUpdate()->find($pakanBaruId);

            if ($pakanLama) {
                $pakanLama->increment('stok', $jumlahLama);
            }

            if (!$pakanBaru || $pakanBaru->stok < $jumlahBaru) {
                throw new \Exception('Stok pakan baru tidak mencukupi.');
            }

            $pakanBaru->decrement('stok', $jumlahBaru);

        } else {

            $pakan = Pakan::lockForUpdate()->find($pakanBaruId);

            $selisih = $jumlahBaru - $jumlahLama;

            if ($selisih > 0 && $pakan->stok < $selisih) {
                throw new \Exception('Stok tidak mencukupi untuk update.');
            }

            $pakan->decrement('stok', $selisih);
        }
    });

    // =========================
    // DELETE
    // =========================
    static::deleting(function ($model) {

        $pakan = Pakan::lockForUpdate()->find($model->pakan_id);

        if ($pakan) {
            $pakan->increment('stok', $model->jumlah);
        }
    });
}
    public function ternak()
    {
        return $this->belongsTo(\App\Models\Ternak::class);
    }

    public function pakan()
    {
        return $this->belongsTo(Pakan::class);
    }
}
