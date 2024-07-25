<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthorResource;
use App\Services\AuthorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    protected $authorService;

    public function __construct(AuthorService $authorService)
    {
        $this->authorService = $authorService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $authors = $this->authorService->getPaginate($request);

        return $this->responseSuccessPaginate(AuthorResource::collection($authors));
    }

    public function getAll(): JsonResponse
    {
        $authors = $this->authorService->getAll();

        return $this->responseSuccess(AuthorResource::collection($authors));
    }

    public function show(int $id): JsonResponse
    {
        $author = $this->authorService->find($id);

        return $this->responseSuccess(AuthorResource::make($author));
    }
}
