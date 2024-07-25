<?php

// app/Traits/ApiResponseTrait.php

namespace App\Traits;

use App\Utils\Constants;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    /**
     * Response success with data.
     *
     * @param mixed|null $data
     */
    public function responseSuccess($data = null, int $code = Response::HTTP_OK): JsonResponse
    {
        $output = [
            'success' => Constants::SUCCESS_OK,
            'data' => $data,
        ];

        return response()->json($output, $code);
    }

    public function responseSuccessPaginate($data, int $code = Response::HTTP_OK): JsonResponse
    {
        if ($data instanceof \Illuminate\Http\Resources\Json\AnonymousResourceCollection) {
            $pagination = $data->response()->getData(true);
            $output = [
                'success' => Constants::SUCCESS_OK,
                'data' => $pagination['data'],
                'pagination' => [
                    'current_page' => $pagination['meta']['current_page'],
                    'per_page' => $pagination['meta']['per_page'],
                    'last_page' => $pagination['meta']['last_page'],
                ],
            ];
        } else {
            $output = [
                'success' => Constants::SUCCESS_OK,
                'data' => $data,
            ];
        }

        return response()->json($output, $code);
    }

    /**
     * Response error with message and error code.
     *
     * @param int $errorCode
     * @param mixed|null $data
     */
    public function responseError(int $code, string $message, $data = null): JsonResponse
    {
        $output = [
            'success' => Constants::SUCCESS_FALSE,
            'data' => $data,
            'errors' => [
                'error_message' => $message,
            ],
        ];

        return response()->json($output, $code);
    }
}
