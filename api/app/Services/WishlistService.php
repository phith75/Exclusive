<?php

namespace App\Services;

use App\Models\Wishlist;
use App\Repositories\WishlistRepository;
use App\Utils\Constants;
use Illuminate\Support\Collection;

class WishlistService
{
    protected $wishlistRepo;

    public function __construct(WishlistRepository $wishlistRepo)
    {
        $this->wishlistRepo = $wishlistRepo;
    }

    public function getAll(): Collection
    {
        $id = auth()->user()->id;

        return $this->wishlistRepo->getBookWishList($id);
    }

    public function create(int $bookId): int|Wishlist
    {
        $id_user = auth()->user()->id;
        $check = $this->wishlistRepo->createOrDeleteWishlist($bookId, $id_user);

        return $check;
    }

    public function delete(int|array $id): int
    {
        return $this->wishlistRepo->delete($id);
    }

    public function getWishlistUser(int $id)
    {
        $perPage = Constants::DEFAULT_PER_PAGE;
        $relation = ['user', 'book'];
        $wishlists = $this->wishlistRepo->getWishlistUser($id, $perPage, $relation);

        return $wishlists;
    }

    public function getSuggestion(int $id): Collection
    {
        return $this->wishlistRepo->getUserSuggestion($id);
    }
}
