<?php

declare(strict_types=1);

namespace App\Base;

use App\Models\ErrorLog;
use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    /**
     * Model
     */
    protected Model $model;

    /**
     * Create a new class instance.
     */
    public function __construct(Model $model)
    {
        $this->model = new $model();
    }

    /**
     * Membuat (insert) catatan error.
     */
    protected function errorLog(string $function, string $message): void
    {
        try {
            ErrorLog::query()->create([
                'function' => $function,
                'message' => $message,
            ]);
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
