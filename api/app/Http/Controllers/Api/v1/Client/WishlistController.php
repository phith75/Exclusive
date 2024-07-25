<?php

namespace App\Http\Controllers\Api\v1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Resources\WishlistResource;
use App\Services\WishlistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class wishlistController extends Controller
{
    protected $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
    }

    public function getAll(): JsonResponse
    {
        $wishlists = $this->wishlistService->getAll();

        return $this->responseSuccess(BookResource::collection($wishlists));
    }

    public function index(): JsonResponse
    {
        $id = auth()->user()->id;
        $wishlists = $this->wishlistService->getWishlistUser($id);

        return $this->responseSuccess(wishlistResource::collection($wishlists));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);
        $idBook = $request->get('book_id');
        $wishlist = $this->wishlistService->create($idBook);

        return $this->responseSuccess($wishlist, Response::HTTP_CREATED);
    }

    public function destroy(int $id): JsonResponse
    {
        $delete = $this->wishlistService->delete($id);

        return $this->responseSuccess($delete);
    }

    public function getSuggestion(): JsonResponse
    {
        $id = auth()->user()->id;
        $suggestion = $this->wishlistService->getSuggestion($id);

        return $this->responseSuccess(BookResource::collection($suggestion));
    }
}
