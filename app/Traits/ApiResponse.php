<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function success($data, ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error($data, ?string $message = null, int $code = 404): JsonResponse
    {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function validationErrors($data, ?string $message = null, int $code = 422): JsonResponse
    {
        return response()->json([
            'status' => 'Error',
            'message' => $message,
            'errors' => $data,
        ], $code);
    }
}
