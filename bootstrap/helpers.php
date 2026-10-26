<?php

declare(strict_types=1);

use App\Application\Module\UseCases\GetModulesByRoleId;
use App\Application\Setting\UseCases\FindSetting;
use App\Domain\Module\Entities\ModuleEntity;
use App\Domain\Setting\Entities\SettingEntity;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

if (! function_exists('parse_date_time_immutable')) {

    /**
     * Merubah string waktu menjadi class DateTimeImmutable
     */
    function parse_date_time_immutable(?string $value): ?DateTimeImmutable
    {
        return $value === null ? null : new DateTimeImmutable($value);
    }
}

if (! function_exists('parse_int')) {

    /**
     * Merubah string numerik menjadi bilangan bulat.
     */
    function parse_int(string|float|int $value): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        throw new InvalidArgumentException('Argument value harus bertipe numerik');
    }
}

if (! function_exists('parse_bool')) {

    /**
     * Merubah string atau int menjadi boolean
     */
    function parse_bool(int|string|bool $value): bool
    {
        if ($value === 1 || $value === '1' || $value === 'true' || $value === true) {
            return true;
        }

        return false;
    }
}

if (! function_exists('modules')) {

    /**
     * Mengambil daftar akses module berdasarkan role
     *
     * @return ModuleEntity[]
     */
    function modules(): array
    {
        static $modules = null;

        if ($modules === null) {
            $modules = app(GetModulesByRoleId::class)->execute(
                roleId: Auth::user()->role_id,
                isRead: true,
            );
        }

        return $modules;
    }
}

if (! function_exists('setting')) {

    /**
     * Mengambil data pengaturan.
     */
    function setting(): ?SettingEntity
    {
        static $setting = null;

        if ($setting === null) {
            $setting = app(FindSetting::class)->execute();
        }

        return $setting;
    }
}

if (! function_exists('user')) {

    /**
     * User login
     */
    function user(): ?Authenticatable
    {
        return Auth::user();
    }
}

if (! function_exists('storage_asset')) {

    /**
     * Storage assets.
     */
    function storage_asset(string $path, ?bool $secure = null): string
    {
        $path = ltrim(trim($path), '/');

        return asset("storage/$path", $secure);
    }
}

if (! function_exists('storage_image')) {

    /**
     * Mengambil gambar dari storage
     */
    function storage_image(?string $path, ?string $default = null, ?bool $secure = null): string
    {
        if (is_string($path)) {
            return asset(
                path: 'storage/'.ltrim(trim($path), '/'),
                secure: $secure
            );
        }

        if (is_string($default)) {
            return asset(
                path: $default,
                secure: $secure,
            );
        }

        return asset(
            path: 'assets/images/empty.jpg',
            secure: $secure,
        );
    }
}

if (! function_exists('user_photo')) {

    /**
     * User photo.
     */
    function user_photo(?string $path = null): string
    {
        if ($path === null) {
            return asset('assets/images/user.webp');
        }

        return storage_asset($path);
    }
}

if (! function_exists('active_path')) {

    /**
     * memetiksa path url aktif atau tidak
     */
    function active_path(string $path, bool $strict = false): bool
    {
        $path = ltrim($path, '/');

        return Request::is($strict ? $path : "$path*");
    }
}

if (! function_exists('pos_connection')) {

    /**
     * pos database connection
     */
    function pos_connection(): string
    {
        return 'pos';
    }
}

if (! function_exists('ulid')) {

    /**
     * ULID
     */
    function ulid(): string
    {
        return strtolower((string) Str::ulid());
    }
}

if (! function_exists('uuid')) {

    /**
     * UUID
     */
    function uuid(): string
    {
        return strtolower((string) Str::uuid());
    }
}

if (! function_exists('parse_float')) {

    /**
     * Parsing data ke tipe data float
     */
    function parse_float(string|int|float $value): float
    {
        if (is_numeric($value)) {
            return (float) $value;
        }

        throw new InvalidArgumentException('Argument value harus bertipe numerik');
    }
}

if (! function_exists('logo')) {
    function logo(?string $path): string
    {
        if ($path === null) {
            return asset('assets/images/logo.svg');
        }

        $path = ltrim(trim($path), '/');

        return asset("storage/$path");
    }
}

if (! function_exists('logo_data_uri')) {

    /**
     * Mengambil logo sebagai data URI (untuk PDF, karena DomPDF tidak memuat URL remote)
     */
    function logo_data_uri(?string $path): ?string
    {
        // Tentukan file logo: logo toko di storage publik atau logo bawaan aplikasi
        $file = public_path('assets/images/logo.svg');

        if ($path !== null && trim($path) !== '') {
            $stored = storage_path('app/public/'.ltrim(trim($path), '/'));

            if (is_file($stored)) {
                $file = $stored;
            }
        }

        if (! is_file($file)) {
            return null;
        }

        // Tentukan mime type berdasarkan ekstensi
        $mime = match (strtolower(pathinfo($file, PATHINFO_EXTENSION))) {
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => null,
        };

        if ($mime === null) {
            return null;
        }

        return 'data:'.$mime.';base64,'.base64_encode((string) file_get_contents($file));
    }
}

if (! function_exists('months')) {

    /**
     * Mengambil daftar bulan
     */
    function months(): array
    {
        return [
            ['key' => 1, 'name' => 'Januari'],
            ['key' => 2, 'name' => 'Februari'],
            ['key' => 3, 'name' => 'Maret'],
            ['key' => 4, 'name' => 'April'],
            ['key' => 5, 'name' => 'Mei'],
            ['key' => 6, 'name' => 'Juni'],
            ['key' => 7, 'name' => 'Juli'],
            ['key' => 8, 'name' => 'Agustus'],
            ['key' => 9, 'name' => 'September'],
            ['key' => 10, 'name' => 'Oktober'],
            ['key' => 11, 'name' => 'November'],
            ['key' => 12, 'name' => 'Desember'],
        ];
    }
}

if (! function_exists('month')) {

    /**
     * Mengambil nama bulan
     */
    function month(int $key): ?string
    {
        $months = months();

        return $months[$key + 1];
    }
}

if (! function_exists('str_random')) {

    /**
     * Random string.
     */
    function str_random(int $length = 32): string
    {
        return Str::random($length);
    }
}
