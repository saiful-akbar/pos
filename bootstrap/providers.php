<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\RepositoryServiceProvider;
use Maatwebsite\Excel\ExcelServiceProvider;

return [
    AppServiceProvider::class,
    RepositoryServiceProvider::class,
    ExcelServiceProvider::class,
];
