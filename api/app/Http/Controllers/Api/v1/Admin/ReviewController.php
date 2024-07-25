<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Enum\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Services\ReviewService;
use App\Utils\Constants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return $this->responseSuccess(ReviewResource::collection($reviews));
    }

    public function index(Request $request): JsonResponse
    {

        $reviews = $this->reviewService->getPaginate($request);

        return $this->responseSuccessPaginate(ReviewResource::collection($reviews));
    }

    public function update(ReviewRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $review = $this->reviewService->update($id, $data);

        return $this->responseSuccess($review);
    }

    public function updateStatus(int $id, Request $request): JsonResponse
    {
        $validate = $request->validate([
            'status' => 'required|in:' . ReviewStatus::ACTIVE->value . ',' . ReviewStatus::INACTIVE->value,
        ]);
        $status = $this->reviewService->updateStatus($id, $validate['status']);

        return $this->responseSuccess($status);
    }

    public function destroy(int $id): JsonResponse
    {
        $delete = $this->reviewService->delete($id);

        return $this->responseSuccess($delete);
    }

    public function deleteMany(Request $request): JsonResponse
    {
        $deleted = $this->reviewService->delete($request->ids);

        return $this->responseSuccess($deleted);
    }

    public function restore(Request $request): JsonResponse
    {
        $restore = $this->reviewService->restore($request->ids);

        return $this->responseSuccess($restore);
    }

    public function getTrashed(Request $request): JsonResponse
    {
        $reviews = $this->reviewService->getTrashed($request);

        return $this->responseSuccess(ReviewResource::collection($reviews));
    }
}
