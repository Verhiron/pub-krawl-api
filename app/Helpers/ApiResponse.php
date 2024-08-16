<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Standardized success response
     *
     * @param mixed|null $data
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public static function success(mixed $data = null, string $message = "Success", int $statusCode = 200): JsonResponse
    {
        return response()->json([
            "success" => true,
            "data" => $data,
            "message" => $message,
        ], $statusCode);
    }


    /**
     * Standardized error response
     *
     * @param string $message
     * @param int $statusCode
     * @param mixed|null $errors
     * @return JsonResponse
     */
    public static function error(string $message = 'Error', int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], $statusCode);
    }

}
