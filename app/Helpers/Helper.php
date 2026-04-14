<?php
namespace App\Helpers;
use Illuminate\Http\Response;

class Helper
{
    public static function validationErrorResponse($message)
    {
        return response()->json(['message' => $message, 'data' => [], 'code' => Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
