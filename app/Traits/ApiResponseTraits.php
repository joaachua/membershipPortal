<?php

namespace App\Traits;

trait ApiResponseTraits
{
    public function successResponse($message, $statusCode, $data = null)
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    public function errorResponse($message, $statusCode = 500, $errors = null)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}