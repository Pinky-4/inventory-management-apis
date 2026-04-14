<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
     /**
     * Returns a JSON response with a success message, data, and HTTP status code.
     *
     * @param array  $data    The data to be included in the response. Default is an empty array.
     * @param string $message The success message to be included in the response. Default is an empty string.
     *
     * @return \Illuminate\Http\JsonResponse the JSON response with the success message, data, and HTTP status code
     */
    public function successResponse($data = [], $message = '')
    {
        $data = !empty($data) ? $data : (object) [];
        return response()->json(['message' => $message, 'data' => $data, 'code' => Response::HTTP_OK], Response::HTTP_OK);
    }

    /**
     * Returns a JSON response with an error message, an empty data object, and an HTTP status code of 500.
     *
     * @param string $message The error message to be included in the response. Default is 'something went wrong'.
     *
     * @return \Illuminate\Http\JsonResponse the JSON response with the error message, an empty data object, and an HTTP status code of 500
     */
    public function errorResponse($message = 'something went wrong')
    {
        return response()->json(['message' => $message, 'data' => (object) [], 'code' => Response::HTTP_INTERNAL_SERVER_ERROR], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Returns a JSON response with a validation error message, data, and HTTP status code.
     *
     * @param string $message the validation error message to be included in the response
     * @param array  $data    The data to be included in the response. Default is an empty array.
     *
     * @return \Illuminate\Http\JsonResponse the JSON response with the validation error message, data, and HTTP status code
     */
    public function validationErrorResponse($message)
    {
        return response()->json(['message' => $message, 'data' => (object) [], 'code' => Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
    /**
     * All request validation created response return
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptedResponse($data = [], $message = '')
    {
        $data = !empty($data) ? $data : (object) [];
        return response()->json(['message' => $message, 'data' => $data,  'code' => Response::HTTP_ACCEPTED], Response::HTTP_ACCEPTED);
    }
    /**
     * All request validation created response return
     *
     * @param  string  $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function createdResponse($message)
    {
        return response()->json(['message' => $message, 'data' => (object) [], 'code' => Response::HTTP_CREATED], Response::HTTP_CREATED);
    }
}
