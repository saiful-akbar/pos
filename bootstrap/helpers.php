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
            $modules = app(GetModulesByRoleId::class)->execute(Auth::user()->role_id);
        }

        return $modules;
    }
}

if (!function_exists('setting')) {

    /**
     * Mengambil data pengaturan.
     *
     * @return SettingEntity|null
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

if (!function_exists('user')) {

    /**
     * User login
     */
    function user(): ?Authenticatable
    {
        return Auth::user();
    }
}

if (!function_exists('storage_asset')) {

    /**
     * Storage assets.
     *
     * @param string $path
     * @param boolean|null $secure
     * @return string
     */
    function storage_asset(string $path, ?bool $secure = null): string
    {
        $path = ltrim(trim($path), '/');

        return asset("storage/$path", $secure);
    }
}

if (!function_exists('user_photo')) {

    /**
     * User photo.
     *
     * @return string
     */
    function user_photo(): string
    {
        if (is_null(user()->photo)) {
            return asset('assets/images/user.webp');
        }

        return storage_asset(user()->photo);
    }
}

/**
 * memetiksa path url aktif atau tidak
 */
if (!function_exists('active_path')) {
    function active_path(string $path, bool $strict = false): bool
    {
        $path = ltrim($path, '/');

        return Request::is($strict ? $path : "$path*");
    }
}
