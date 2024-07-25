<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->getPaginate($request);

        return $this->responseSuccessPaginate(CategoryResource::collection($categories));
    }

    public function getAll(): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        return $this->responseSuccess(CategoryResource::collection($categories));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $newCategory = $this->categoryService->create($validatedData);

        return $this->responseSuccess($newCategory, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->find($id);

        return $this->responseSuccess(CategoryResource::make($category));
    }

    public function update(CategoryRequest $request, int $id): JsonResponse
    {
        $validatedData = $request->validated();
        $updatedCategory = $this->categoryService->update($id, $validatedData);

        return $this->responseSuccess($updatedCategory);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->categoryService->delete($id);

        return $this->responseSuccess($deleted);
    }

    public function deleteMany(Request $request): JsonResponse
    {
        $deleted = $this->categoryService->delete($request->ids);

        if ($deleted == 0) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, 'Category still exist in book, cannot be deleted');
        }

        return $this->responseSuccess($deleted);
    }

    public function getTrashed(Request $request): JsonResponse
    {

        $categories = $this->categoryService->getTrashed($request);

        return $this->responseSuccessPaginate(CategoryResource::collection($categories));
    }

    public function restore(Request $request): JsonResponse
    {
        $restoredCategory = $this->categoryService->restore($request->ids);

        return $this->responseSuccess($restoredCategory);
    }
}
