<?php

declare(strict_types=1);

use App\Application\Module\UseCases\GetModulesByRoleId;
use App\Application\Setting\UseCases\FindSetting;
use App\Domain\Module\Entities\ModuleEntity;
use App\Domain\Setting\Entities\SettingEntity;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

if (! function_exists('parse_date_time_immutable')) {

    /**
     * Merubah string waktu menjadi class DateTimeImmutable
     */
    function parse_date_time_immutable(?string $value): DateTimeImmutable
    {
        return $value === null ? null : new DateTimeImmutable($value);
    }
}

if (! function_exists('parse_int')) {

    /**
     * Merubah string numerik menjadi bilangan bulat.
     */
    function parse_int(null|string|float|int $value): null|string|float|int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return $value;
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

if (! function_exists('user_photo')) {

    /**
     * User photo.
     */
    function user_photo(): string
    {
        if (user()->photo === null) {
            return asset('assets/images/user.webp');
        }

        return storage_asset(user()->photo);
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
