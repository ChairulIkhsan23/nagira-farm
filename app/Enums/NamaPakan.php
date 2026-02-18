<?php

namespace App\Enums;

enum NamaPakan: string
{
    // ===============================
    // 🌿 HIJAUAN
    // ===============================
    case RUMPUT_ODOT = 'Rumput Odot';
    case RUMPUT_GAJAH = 'Rumput Gajah';
    case RUMPUT_RAJA = 'Rumput Raja';
    case RUMPUT_SETARIA = 'Rumput Setaria';
    case RUMPUT_KOLONJONO = 'Rumput Kolonjono';
    case DAUN_LAMTORO = 'Daun Lamtoro';
    case DAUN_KALIANDRA = 'Daun Kaliandra';
    case DAUN_TURI = 'Daun Turi';
    case DAUN_SINGKONG = 'Daun Singkong';
    case DAUN_NANGKA = 'Daun Nangka';
    case DAUN_PEPAYA = 'Daun Pepaya';
    case DAUN_JAGUNG = 'Daun Jagung';
    case JERAMI_PADI = 'Jerami Padi';
    case JERAMI_KACANG = 'Jerami Kacang Tanah';

    // ===============================
    // 🌾 KONSENTRAT
    // ===============================
    case DEDAK_HALUS = 'Dedak Halus';
    case DEDAK_KASAR = 'Dedak Kasar';
    case JAGUNG_GILING = 'Jagung Giling';
    case POLLARD = 'Pollard';
    case KONSENTRAT_781 = 'Konsentrat 781-2';
    case KONSENTRAT_PEDAGING = 'Konsentrat Kambing Pedaging';
    case KONSENTRAT_PERAH = 'Konsentrat Kambing Perah';
    case TEPUNG_JAGUNG = 'Tepung Jagung';
    case TEPUNG_KEDELAI = 'Tepung Kedelai';
    case TEPUNG_IKAN = 'Tepung Ikan';
    case TEPUNG_SINGKONG = 'Tepung Singkong';

    // ===============================
    // 🌽 SILASE
    // ===============================
    case SILASE_JAGUNG = 'Silase Jagung';
    case SILASE_RUMPUT_GAJAH = 'Silase Rumput Gajah';
    case SILASE_SORGUM = 'Silase Sorgum';
    case SILASE_DAUN_JAGUNG = 'Silase Daun Jagung';

    // ===============================
    // 🏭 LIMBAH & BUNGKIL
    // ===============================
    case AMPAS_TAHU = 'Ampas Tahu';
    case AMPAS_KELAPA = 'Ampas Kelapa';
    case AMPAS_BIR = 'Ampas Bir';
    case BUNGKIL_KEDELAI = 'Bungkil Kedelai';
    case BUNGKIL_KELAPA = 'Bungkil Kelapa';
    case BUNGKIL_SAWIT = 'Bungkil Sawit';
    case KULIT_KOPI = 'Kulit Kopi';
    case KULIT_ARI_KEDELAI = 'Kulit Ari Kedelai';

    // ===============================
    // 🧂 MINERAL & SUPLEMEN
    // ===============================
    case MINERAL_MIX = 'Mineral Mix';
    case MINERAL_BLOK = 'Mineral Blok';
    case GARAM_TERNAK = 'Garam Ternak';
    case VITAMIN_ADE = 'Vitamin ADE';
    case VITAMIN_B_COMPLEX = 'Vitamin B Complex';
    case PROBIOTIK_TERNAK = 'Probiotik Ternak';
    case EM4_PETERNAKAN = 'EM4 Peternakan';

    // ===============================
    // 🍼 PAKAN ANAK
    // ===============================
    case STARTER_KAMBING = 'Starter Kambing';
    case SUSU_REPLACER = 'Susu Bubuk Pengganti Induk';

    // ===============================
    // 🔥 MAPPING JENIS -> NAMA
    // ===============================
    public static function byJenis(string $jenis): array
    {
        return match ($jenis) {

            'Hijauan',
            'Leguminosa',
            'Daun-daunan' => [
                self::RUMPUT_ODOT,
                self::RUMPUT_GAJAH,
                self::RUMPUT_RAJA,
                self::RUMPUT_SETARIA,
                self::RUMPUT_KOLONJONO,
                self::DAUN_LAMTORO,
                self::DAUN_KALIANDRA,
                self::DAUN_TURI,
                self::DAUN_SINGKONG,
                self::DAUN_NANGKA,
                self::DAUN_PEPAYA,
                self::DAUN_JAGUNG,
                self::JERAMI_PADI,
                self::JERAMI_KACANG,
            ],

            'Konsentrat Energi',
            'Konsentrat Protein' => [
                self::DEDAK_HALUS,
                self::DEDAK_KASAR,
                self::JAGUNG_GILING,
                self::POLLARD,
                self::KONSENTRAT_781,
                self::KONSENTRAT_PEDAGING,
                self::KONSENTRAT_PERAH,
                self::TEPUNG_JAGUNG,
                self::TEPUNG_KEDELAI,
                self::TEPUNG_IKAN,
                self::TEPUNG_SINGKONG,
            ],

            'Silase',
            'Fermentasi' => [
                self::SILASE_JAGUNG,
                self::SILASE_RUMPUT_GAJAH,
                self::SILASE_SORGUM,
                self::SILASE_DAUN_JAGUNG,
            ],

            'Limbah Pertanian',
            'Bungkil' => [
                self::AMPAS_TAHU,
                self::AMPAS_KELAPA,
                self::AMPAS_BIR,
                self::BUNGKIL_KEDELAI,
                self::BUNGKIL_KELAPA,
                self::BUNGKIL_SAWIT,
                self::KULIT_KOPI,
                self::KULIT_ARI_KEDELAI,
            ],

            'Mineral',
            'Vitamin',
            'Probiotik' => [
                self::MINERAL_MIX,
                self::MINERAL_BLOK,
                self::GARAM_TERNAK,
                self::VITAMIN_ADE,
                self::VITAMIN_B_COMPLEX,
                self::PROBIOTIK_TERNAK,
                self::EM4_PETERNAKAN,
            ],

            'Starter',
            'Susu Pengganti' => [
                self::STARTER_KAMBING,
                self::SUSU_REPLACER,
            ],

            default => [],
        };
    }

    public static function optionsByJenis(?string $jenis): array
    {
        if (!$jenis) {
            return [];
        }

        return collect(self::byJenis($jenis))
            ->mapWithKeys(fn ($case) => [$case->value => $case->value])
            ->toArray();
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
