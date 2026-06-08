<?php

namespace App\Enums;

enum JenisPakan: string
{
    
    
    
    case HIJAUAN = 'Hijauan';
    case LEGUMINOSA = 'Leguminosa';
    case DAUN_DAUNAN = 'Daun-daunan';

    
    
    
    case KONSENTRAT_ENERGI = 'Konsentrat Energi';
    case KONSENTRAT_PROTEIN = 'Konsentrat Protein';

    
    
    
    case SILASE = 'Silase';
    case FERMENTASI = 'Fermentasi';

    
    
    
    case LIMBAH_PERTANIAN = 'Limbah Pertanian';
    case BUNGKIL = 'Bungkil';

    
    
    
    case MINERAL = 'Mineral';
    case VITAMIN = 'Vitamin';
    case PROBIOTIK = 'Probiotik';

    
    
    
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
