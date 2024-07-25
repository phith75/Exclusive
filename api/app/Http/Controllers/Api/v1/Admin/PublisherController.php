<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PublisherRequest;
use App\Http\Resources\PublisherResource;
use App\Services\PublisherService;
use App\Utils\Constants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublisherController extends Controller
{
    protected $publisherService;

    public function __construct(PublisherService $publisherService)
    {
        $this->publisherService = $publisherService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $publishers = $this->publisherService->getPaginate($request);

        return $this->responseSuccessPaginate(PublisherResource::collection($publishers));
    }

    public function getAll(): JsonResponse
    {
        $PubLishers = $this->publisherService->getAll();

        return $this->responseSuccess(PublisherResource::collection($PubLishers));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PublisherRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $newPublisher = $this->publisherService->create($validatedData);

        return $this->responseSuccess($newPublisher, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $publisher = $this->publisherService->find($id);

        return $this->responseSuccess(PublisherResource::make($publisher));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PublisherRequest $request, int $id): JsonResponse
    {
        $validatedData = $request->validated();
        $updatedPublisher = $this->publisherService->update($id, $validatedData);

        return $this->responseSuccess($updatedPublisher);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->publisherService->delete($id);

        if ($deleted == 0) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, 'Publisher still exist in book, cannot be deleted');
        }

        return $this->responseSuccess('Publisher deleted successfully');
    }

    public function deleteMany(Request $request): JsonResponse
    {
        $deleted = $this->publisherService->delete($request->ids);
        if ($deleted == 0) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, 'Some publisher still exist in book, cannot be deleted');
        }

        return $this->responseSuccess($deleted);
    }
    /**
     * Get a list of soft-deleted publishers.
     */
    public function getTrashed(Request $request): JsonResponse
    {
        $publishers = $this->publisherService->getTrashed($request);

        return $this->responseSuccessPaginate(PublisherResource::collection($publishers));
    }

    public function restore(Request $request): JsonResponse
    {
        $restoredPublisher = $this->publisherService->restore($request->ids);

        return $this->responseSuccess($restoredPublisher);
    }
}
