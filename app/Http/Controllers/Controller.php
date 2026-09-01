<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Json response.
     */
    protected function jsonResponse(mixed $data = null, mixed $meta = null, int $status = 200, string $message = 'Ok'): JsonResponse
    {
        return response()->json(
            status: $status,
            data: [
                'data' => $data,
                'meta' => $meta,
                'status' => $status,
                'message' => $message,
            ],
        );
    }
}
