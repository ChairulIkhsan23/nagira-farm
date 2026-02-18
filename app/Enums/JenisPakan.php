<?php

namespace App\Enums;

enum JenisPakan: string
{
    // ===============================
    // 🌿 HIJAUAN
    // ===============================
    case HIJAUAN = 'Hijauan';
    case LEGUMINOSA = 'Leguminosa';
    case DAUN_DAUNAN = 'Daun-daunan';

    // ===============================
    // 🌾 KONSENTRAT
    // ===============================
    case KONSENTRAT_ENERGI = 'Konsentrat Energi';
    case KONSENTRAT_PROTEIN = 'Konsentrat Protein';

    // ===============================
    // 🌽 SILASE & FERMENTASI
    // ===============================
    case SILASE = 'Silase';
    case FERMENTASI = 'Fermentasi';

    // ===============================
    // 🏭 LIMBAH & BUNGKIL
    // ===============================
    case LIMBAH_PERTANIAN = 'Limbah Pertanian';
    case BUNGKIL = 'Bungkil';

    // ===============================
    // 🧂 MINERAL & SUPLEMEN
    // ===============================
    case MINERAL = 'Mineral';
    case VITAMIN = 'Vitamin';
    case PROBIOTIK = 'Probiotik';

    // ===============================
    // 🍼 PAKAN ANAK
    // ===============================
    case STARTER = 'Starter';
    case SUSU_PENGGANTI = 'Susu Pengganti';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return $this->value;
    }
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->value])
            ->toArray();
    }
}
