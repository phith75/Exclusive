<?php

namespace App\Repositories;

use App\Models\Book;
use App\Models\Wishlist;
use App\Utils\Constants;
use Illuminate\Support\Collection;

class WishlistRepository extends BaseRepository
{
    public function __construct(
        protected Book $modelBook
    ) {
        parent::__construct();
    }

    public function getModel(): string
    {
        return Wishlist::class;
    }

    public function getWishlistUser(int $id, int $perPage, array $relation = [])
    {
        return $this->model
            ->with($relation)
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function createOrDeleteWishlist(int $book_id, int $user_id): int|Wishlist
    {
        $check = $this->model->where('book_id', $book_id)->where('user_id', $user_id)->first();
        if ($check) {
            return $check->forceDelete();
        } else {
            $user = $this->model->create([
                'book_id' => $book_id,
                'user_id' => $user_id,
            ]);

            return $user;
        }
    }

    public function getUserSuggestion(int $id)
    {
        $getWishList = $this->model
            ->where('user_id', $id)
            ->pluck('book_id');

        $getCategories = $this->model
            ->where('user_id', $id)
            ->with('book.category')
            ->get()
            ->pluck('book.category_id');
        $getSuggestedBooks = $this->modelBook
            ->whereNotIn('id', $getWishList)
            ->whereIn('category_id', $getCategories)
            ->limit(Constants::LIMIT_QUANTITY)
            ->get();

        return $getSuggestedBooks;
    }

    public function getBookWishList(int $id): Collection
    {
        return Book::join('wishlists', 'books.id', '=', 'wishlists.book_id')
            ->where('wishlists.user_id', $id)
            ->select('books.*', 'wishlists.id as wishlist_id')
            ->get();
    }
}
