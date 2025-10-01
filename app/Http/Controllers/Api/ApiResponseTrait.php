<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * 成功レスポンスを返す
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = '', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $status);
    }

    /**
     * エラーレスポンスを返す
     *
     * @param array $errors
     * @param string $message
     * @param int $status
     * @return JsonResponse
     */
    protected function errorResponse(array $errors = [], string $message = '', int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'errors' => $errors,
            'message' => $message,
        ], $status);
    }
}