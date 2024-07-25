<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->find($id);

        return $this->responseSuccess(CategoryResource::make($category));
    }
}
