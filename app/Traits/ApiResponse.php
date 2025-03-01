<?php

declare(strict_types = 1);

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function sendResponse($token = null, $result, $message): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $result,
        ];

        if ($token !== null) {
            $response['token'] = $token;
        }

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessage = [], $code = 404): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorMessage)) {
            $response['data'] = $errorMessage;
        }

        return response()->json($response, $code);
    }
}
