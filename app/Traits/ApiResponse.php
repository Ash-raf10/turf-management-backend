<?php

namespace App\Traits;

trait ApiResponse
{
    /**
     * SendResponse send json response
     *
     * @param bool        $status     true/false
     * @param mixed $data     data or result
     * @param array|string $message    message to show to the user
     * @param int         $statusCode http status codes
     * @param int         $internalCode for our use
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse(
        bool $status = true,
        mixed $data = "",
        array|string $message = "",
        int $statusCode = 200,
        $internalCode = 0000
    ) {
        $response = [
            'success' => $status,
            'data'    => $data,
            'message' => $message,
            'code' => $internalCode
        ];

        return response()->json($response, $statusCode);
    }
}
