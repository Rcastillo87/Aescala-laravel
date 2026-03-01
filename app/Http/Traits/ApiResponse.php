<?php

namespace App\Http\Traits;

trait ApiResponse
{
    protected function success($data = [], string $message = 'OK', int $code = 200)
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data
        ], $code);
    }

    protected function error(string $message, int $code = 400, $errors = null)
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors
        ], $code);
    }
}