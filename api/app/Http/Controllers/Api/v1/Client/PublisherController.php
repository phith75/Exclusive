<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublisherResource;
use App\Services\PublisherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $publisher = $this->publisherService->find($id);

        return $this->responseSuccess(PublisherResource::make($publisher));
    }
}
