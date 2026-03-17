<?php

namespace App\Helpers;

class ApiResponse{
    public static function success($data = [], $message = 'success', $code = 200){
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data 
        ], $code);
    }

    public static function error($message = 'error', $code = 400){
        return response()->json([
            'success' => false,
            'message' => $message
        ], $code);
    }
}   