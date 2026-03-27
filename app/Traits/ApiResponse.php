<?php
namespace App\Traits;

use Illuminate\Http\JsonResponse;


trait ApiResponse
{

    /**
     * return success response
     * 
     * @param mixed $data
     * @param integer $code
     * @param string $status
     * @return JsonResponse
     */
    protected static final function success(mixed $data=[], int $code=200, string $status='success'):JsonResponse
    {
        return response()->json([
            'data'=>$data,
            'status'=>$status,

        ],$code);
    }

    /**
     * return error response
     * @param array $errors
     * @param string $code
     * @param string $status
     * @return JsonResponse
     */

     protected static final function error($message="server error", array $errors=[], int $code=500, string $status='failed'):JsonResponse
     {
        return response()->json([
            'message'=>$message,
            'errors'=>$errors,
            'status'=>$status
        ],$code);
     }
}