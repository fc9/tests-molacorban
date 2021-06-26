<?php

namespace App\Libraries;

use Illuminate\Http\JsonResponse;

class Response
{
    /**
     * @param $statusCode
     * @param null $data
     * @param null $message
     * @param null $error
     * @return JsonResponse
     */
    public static function json($statusCode, $data = null, $message = null, $error = null, $pretty = false): JsonResponse
    {
        $response = (is_null($error) ? ['success' => true] : ['errors' => $error]) + ($data ?? []);

        if (!is_null($message)) $response['message'] = $message;

        return response()->json($response, $statusCode, [], $pretty ? JSON_PRETTY_PRINT : null);
    }
}