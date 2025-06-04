<?php

/**
 * @author TIL <til@army.mil.bd>
 *
 * @contributor Md. Mostafijur Rahman <[mostafijur.til@gmail.com]>
 *
 * @created 06-01-2025
 */

namespace App\Traits;

trait ApiResponseTrait
{
    public function success($data, $message = '', $code = 200)
    {
        $response = [
            'status' => true,
            'message' => $message,
        ];

        if (is_array($data) && array_key_exists('data', $data)) {
            if (array_key_exists('links', $data)) {
                $response['links'] = $data['links'];
            }
            if (array_key_exists('meta', $data)) {
                $response['meta'] = $data['meta'];
            }
            $response['data'] = $data['data'];
        } else {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    // public function success($data, $message = '', $code = 200)
    // {
    //     return response()->json([
    //         'status' => true,
    //         'message' => $message,
    //         'data' => $data,
    //     ], $code);
    // }

    public function error($message = 'Something went wrong', $code = 500)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => '',
        ], $code);
    }
}
