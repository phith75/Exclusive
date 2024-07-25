<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorRequest;
use App\Http\Resources\AuthorResource;
use App\Services\AuthorService;
use App\Utils\Constants;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(AuthorRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $newAuthor = $this->authorService->create($validatedData);

        return $this->responseSuccess($newAuthor, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $author = $this->authorService->find($id);

        return $this->responseSuccess(AuthorResource::make($author));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AuthorRequest $request, int $id): JsonResponse
    {
        $validatedData = $request->validated();
        $updatedAuthor = $this->authorService->update($id, $validatedData);

        return $this->responseSuccess($updatedAuthor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->authorService->delete($id);

        if ($deleted == 0) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, 'Author still exist in book, cannot be deleted');
        }

        return $this->responseSuccess('Author deleted successfully');
    }

    public function deleteMany(Request $request): JsonResponse
    {
        $deleted = $this->authorService->delete($request->ids);
        if ($deleted == 0) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, 'Some author still exist in book, cannot be deleted');
        }

        return $this->responseSuccess($deleted);
    }

    /**
     * Get a list of soft-deleted authors.
     */
    public function getTrashed(Request $request): JsonResponse
    {
        $authors = $this->authorService->getTrashed($request);

        return $this->responseSuccessPaginate(AuthorResource::collection($authors));
    }

    /**
     * Restore a soft-deleted author.
     */
    public function restore(Request $request): JsonResponse
    {
        $restoredAuthor = $this->authorService->restore($request->ids);

        return $this->responseSuccess($restoredAuthor);
    }
}
