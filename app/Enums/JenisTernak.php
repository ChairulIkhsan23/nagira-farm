<?php

namespace App\Enums;

enum JenisTernak: string
{
    // ===============================
    // 🇮🇩 KAMBING LOKAL INDONESIA
    // ===============================
    case KAMBING_KACANG = 'Kambing Kacang';
    case KAMBING_JAWARANDU = 'Kambing Jawarandu';
    case KAMBING_ETAWA = 'Kambing Etawa';
    case KAMBING_PERANAKAN_ETAWA = 'Kambing Peranakan Etawa';

    // ===============================
    // KAMBING IMPOR POPULER
    // ===============================
    case KAMBING_BOER = 'Kambing Boer';
    case KAMBING_SAANEN = 'Kambing Saanen';
    case KAMBING_ALPINE = 'Kambing Alpine';
    case KAMBING_TOGGENBURG = 'Kambing Toggenburg';
    case KAMBING_ANGLO_NUBIAN = 'Kambing Anglo Nubian';

    // ===============================
    // KHUSUS PEDAGING
    // ===============================
    case KAMBING_KIKO = 'Kambing Kiko';
    case KAMBING_MYOTONIC = 'Kambing Myotonic (Fainting Goat)';

    // ===============================
    // KHUSUS SUSU
    // ===============================
    case KAMBING_LA_MANCHA = 'Kambing LaMancha';
    case KAMBING_OBERHASLI = 'Kambing Oberhasli';
    
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::KAMBING_KACANG => 'Kambing Kacang',
            self::KAMBING_JAWARANDU => 'Kambing Jawarandu',
            self::KAMBING_ETAWA => 'Kambing Etawa',
            self::KAMBING_PERANAKAN_ETAWA => 'Kambing Peranakan Etawa',
            self::KAMBING_BOER => 'Kambing Boer',
            self::KAMBING_SAANEN => 'Kambing Saanen',
            self::KAMBING_ALPINE => 'Kambing Alpine',
            self::KAMBING_TOGGENBURG => 'Kambing Toggenburg',
            self::KAMBING_ANGLO_NUBIAN => 'Kambing Anglo Nubian',
            self::KAMBING_KIKO => 'Kambing Kiko',
            self::KAMBING_MYOTONIC => 'Kambing Myotonic (Fainting Goat)',
            self::KAMBING_LA_MANCHA => 'Kambing LaMancha',
            self::KAMBING_OBERHASLI => 'Kambing Oberhasli',
        };
    }
    
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}