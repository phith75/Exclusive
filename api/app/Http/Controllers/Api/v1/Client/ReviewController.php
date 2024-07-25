<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Enum\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function getAll(): JsonResponse
    {
        $reviews = $this->reviewService->getAll();

        return $this->responseSuccess(reviewResource::collection($reviews));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $reviews = $this->reviewService->find($id);

        return $this->responseSuccessPaginate(ReviewResource::collection($reviews));
    }

    public function store(ReviewRequest $request): JsonResponse
    {
        $idUser = auth()->user()->id;
        $data = $request->validated();
        $data['user_id'] = $idUser;
        $review = $this->reviewService->create($data);

        return $this->responseSuccess($review, Response::HTTP_CREATED);
    }

    public function destroy(int $id): JsonResponse
    {
        $delete = $this->reviewService->delete($id);

        return $this->responseSuccess($delete);
    }
}
