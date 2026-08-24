<?php

declare(strict_types=1);

use App\Application\Module\UseCases\GetModulesByRoleId;
use App\Domain\Module\Entities\ModuleEntity;
use Illuminate\Support\Facades\Auth;

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
