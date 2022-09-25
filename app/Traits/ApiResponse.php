<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponse
{
    /**
     * @param null $data
     * @param string $message
     * @param int $httpCode
     * @return JsonResponse
     */
    public function successResponse(
        $data = null,
        string $message = 'Success',
        int $httpCode = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'message' => $message,
            'code' => $httpCode
        ], $httpCode);
    }

    /**
     * @param array|string|null $message
     * @param int $httpCode
     * @return JsonResponse
     */
    public function errorResponse(
        array|string|null $message = 'Something went wrong',
        int $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => $httpCode
        ], $httpCode);
    }
}
