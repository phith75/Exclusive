<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Services\AuthorService;
use App\Services\BookService;
use App\Services\CategoryService;
use App\Services\PublisherService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class BookController extends Controller
{
    protected $bookService;

    protected $authorService;

    protected $categoryService;

    protected $publisherService;

    public function __construct(BookService $bookService, AuthorService $authorService, CategoryService $categoryService, PublisherService $publisherService)
    {
        $this->bookService = $bookService;
        $this->authorService = $authorService;
        $this->categoryService = $categoryService;
        $this->publisherService = $publisherService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $books = $this->bookService->getWithRelation($request);

        return $this->responseSuccessPaginate(BookResource::collection($books));
    }

    public function getAll(): JsonResponse
    {
        $books = $this->bookService->getAll();

        return $this->responseSuccess(BookResource::collection($books));
    }

    public function show(int $id): JsonResponse
    {
        $book = $this->bookService->find($id);

        return $this->responseSuccess(BookResource::make($book));
    }
    // Collection of the most borrowed books: Show the book list most borrowed

    public function getMostBorrowed(): JsonResponse
    {
        $books = $this->bookService->getMostBorrowed();

        return $this->responseSuccess(BookResource::collection($books));
    }
}
