<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum KategoriPengaduan: string implements HasLabel, HasColor, HasIcon
{
    case SARAN = 'saran';
    case KRITIK = 'kritik';
    case KELUHAN = 'keluhan';
    case PERTANYAAN = 'pertanyaan';
    case LAPORAN = 'laporan';
    case INFORMASI = 'informasi';
    case LAINNYA = 'lainnya';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::SARAN => 'Saran',
            self::KRITIK => 'Kritik',
            self::KELUHAN => 'Keluhan',
            self::PERTANYAAN => 'Pertanyaan',
            self::LAPORAN => 'Laporan',
            self::INFORMASI => 'Permintaan Informasi',
            self::LAINNYA => 'Lainnya',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SARAN => 'info',
            self::KRITIK => 'warning',
            self::KELUHAN => 'danger',
            self::PERTANYAAN => 'success',
            self::LAPORAN => 'primary',
            self::INFORMASI => 'gray',
            self::LAINNYA => 'secondary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::SARAN => 'heroicon-m-light-bulb',
            self::KRITIK => 'heroicon-m-chat-bubble-left-ellipsis',
            self::KELUHAN => 'heroicon-m-exclamation-triangle',
            self::PERTANYAAN => 'heroicon-m-question-mark-circle',
            self::LAPORAN => 'heroicon-m-document-text',
            self::INFORMASI => 'heroicon-m-information-circle',
            self::LAINNYA => 'heroicon-m-ellipsis-horizontal-circle',
        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::SARAN => 'Masukan atau ide untuk pengembangan sistem',
            self::KRITIK => 'Kritik membangun terhadap sistem atau layanan',
            self::KELUHAN => 'Keluhan terkait produk atau layanan',
            self::PERTANYAAN => 'Pertanyaan seputar penggunaan sistem',
            self::LAPORAN => 'Laporan terkait bug atau masalah teknis',
            self::INFORMASI => 'Permintaan informasi atau data',
            self::LAINNYA => 'Pengaduan dengan kategori lainnya',
        };
    }

    /**
     * Mendapatkan semua nilai enum untuk opsi select
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [
                $case->value => $case->getLabel(),
            ])
            ->toArray();
    }

    /**
     * Mendapatkan badge color untuk setiap kategori
     */
    public static function getColorByValue(string $value): string
    {
        $enum = self::tryFrom($value);
        return $enum ? $enum->getColor() : 'gray';
    }

    /**
     * Mendapatkan icon untuk setiap kategori
     */
    public static function getIconByValue(string $value): ?string
    {
        $enum = self::tryFrom($value);
        return $enum ? $enum->getIcon() : null;
    }

    /**
     * Mendapatkan semua nilai enum untuk validasi atau migrasi
    */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}