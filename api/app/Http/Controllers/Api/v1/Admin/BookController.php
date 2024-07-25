<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookRequest;
use App\Http\Resources\BookResource;
use App\Services\AuthorService;
use App\Services\BookService;
use App\Services\CategoryService;
use App\Services\PublisherService;
use App\Utils\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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

    public function store(BookRequest $request): JsonResponse
    {
        $newBook = $this->bookService->create($request->validated());

        return $this->responseSuccess($newBook, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): JsonResponse
    {
        $book = $this->bookService->find($id);

        return $this->responseSuccess(BookResource::make($book));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookRequest $request, int $id): JsonResponse
    {
        $updatedBook = $this->bookService->update($id, $request->validated());

        return $this->responseSuccess($updatedBook);
    }

    public function destroy(int $id): JsonResponse
    {
        $delete =  $this->bookService->delete($id);
        if ($delete == 0) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, 'Book still exist in ticket, cannot be deleted');
        }

        return $this->responseSuccess('Book deleted successfully');
    }

    public function deleteMany(Request $request): JsonResponse
    {
        $deleted = $this->bookService->delete($request->ids);

        if ($deleted == 0) {
            return $this->responseError(Response::HTTP_BAD_REQUEST, 'Some book still exist in ticket, cannot be deleted');
        }

        return $this->responseSuccess($deleted);
    }

    /**
     * Get a list of soft-deleted books.
     */
    public function getTrashed(Request $request): JsonResponse
    {

        $books = $this->bookService->getTrashed($request);

        return $this->responseSuccessPaginate(BookResource::collection($books));
    }

    /**
     * Restore a soft-deleted book.
     */
    public function restore(Request $request): JsonResponse
    {
        $restoredBook = $this->bookService->restore($request->ids);

        return $this->responseSuccess($restoredBook);
    }
}
