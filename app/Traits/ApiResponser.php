<?php

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser {

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    protected function successResponse($data, $message = null, $code = Response::HTTP_OK)
    {
        return response()->json([
            'status'  => 'Success', 
            'message' => $message, 
            'data'    => $data
        ], $code);
    }

    /**
     * success response method with token.
     *
     * @return \Illuminate\Http\Response
     */
    protected function successResponseWithToken($token, $data, $message = null, $code = Response::HTTP_OK)
    {
        return response()->json([
            'status'  => 'Success', 
            'message' => $message, 
            'data'    => $data,
            'token'   => $token,
        ], $code);
    }

    /**
     * error response method.
     *
     * @return \Illuminate\Http\Response
     */
    protected function errorResponse($message = null, $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return response()->json([
            'status'  => 'Error',
            'message' => $message,
            'data'    => null
        ], $code);
    }
}