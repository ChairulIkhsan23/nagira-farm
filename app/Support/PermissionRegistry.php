<?php

namespace App\Support;

class PermissionRegistry
{
    public static function definitions(): array
    {
        $definitions = static::pageDefinitions();

        foreach (static::resourceFeatures() as $feature) {
            $definitions = array_merge(
                $definitions,
                static::crudDefinitions(
                    key: $feature['key'],
                    label: $feature['label'],
                    group: $feature['group'],
                ),
            );
        }

        $definitions[] = [
            'slug' => 'view_permissions',
            'name' => 'Lihat Permission',
            'group' => 'Akses',
            'description' => 'Melihat daftar permission sistem',
        ];

        return $definitions;
    }

    public static function slugs(): array
    {
        return array_column(static::definitions(), 'slug');
    }

    public static function defaultRolePermissions(): array
    {
        $adminPermissions = array_column(static::pageDefinitions(), 'slug');

        foreach (static::resourceFeatures() as $feature) {
            if (in_array($feature['key'], ['users', 'roles'], true)) {
                continue;
            }

            $adminPermissions = array_merge($adminPermissions, static::crudSlugs($feature['key']));
        }

        return [
            'superadmin' => static::slugs(),
            'admin' => $adminPermissions,
        ];
    }

    public static function legacySlugs(): array
    {
        return [
            'access_ternak',
            'access_riwayat_timbang',
            'access_fattening',
            'access_perkawinan',
            'access_kelahiran',
            'access_kesehatan',
            'access_pakan',
            'access_pakan_ternak',
            'access_artikel',
            'access_kategori_artikel',
            'access_pengaduan',
            'manage_users',
            'manage_roles',
        ];
    }

    protected static function pageDefinitions(): array
    {
        return [
            ['slug' => 'access_dashboard', 'name' => 'Akses Dashboard', 'group' => 'Umum', 'description' => 'Melihat ringkasan dashboard admin'],
            ['slug' => 'access_pengaturan', 'name' => 'Akses Pengaturan Akun', 'group' => 'Umum', 'description' => 'Mengubah profil dan password akun'],
        ];
    }

    protected static function resourceFeatures(): array
    {
        return [
            ['key' => 'ternak', 'label' => 'Ternak', 'group' => 'Operasional'],
            ['key' => 'riwayat_timbang', 'label' => 'Riwayat Timbang', 'group' => 'Operasional'],
            ['key' => 'fattening', 'label' => 'Fattening', 'group' => 'Operasional'],
            ['key' => 'perkawinan', 'label' => 'Perkawinan', 'group' => 'Operasional'],
            ['key' => 'kelahiran', 'label' => 'Kelahiran', 'group' => 'Operasional'],
            ['key' => 'kesehatan', 'label' => 'Kesehatan', 'group' => 'Operasional'],
            ['key' => 'pakan', 'label' => 'Pakan', 'group' => 'Operasional'],
            ['key' => 'pakan_ternak', 'label' => 'Pakan Ternak', 'group' => 'Operasional'],
            ['key' => 'artikel', 'label' => 'Artikel', 'group' => 'Konten'],
            ['key' => 'kategori_artikel', 'label' => 'Kategori Artikel', 'group' => 'Konten'],
            ['key' => 'pengaduan', 'label' => 'Pengaduan', 'group' => 'Layanan'],
            ['key' => 'users', 'label' => 'Pengguna', 'group' => 'Akses'],
            ['key' => 'roles', 'label' => 'Role', 'group' => 'Akses'],
        ];
    }

    protected static function crudDefinitions(string $key, string $label, string $group): array
    {
        return [
            [
                'slug' => "view_{$key}",
                'name' => "Lihat {$label}",
                'group' => $group,
                'description' => "Melihat data {$label}",
            ],
            [
                'slug' => "create_{$key}",
                'name' => "Tambah {$label}",
                'group' => $group,
                'description' => "Menambahkan data {$label}",
            ],
            [
                'slug' => "update_{$key}",
                'name' => "Ubah {$label}",
                'group' => $group,
                'description' => "Mengubah data {$label}",
            ],
            [
                'slug' => "delete_{$key}",
                'name' => "Hapus {$label}",
                'group' => $group,
                'description' => "Menghapus data {$label}",
            ],
        ];
    }

    protected static function crudSlugs(string $key): array
    {
        return [
            "view_{$key}",
            "create_{$key}",
            "update_{$key}",
            "delete_{$key}",
        ];
    }
}
