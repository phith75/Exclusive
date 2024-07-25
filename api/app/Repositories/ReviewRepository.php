<?php

namespace App\Repositories;

use App\Enum\ReviewStatus;
use App\Models\Book;
use App\Models\Review;
use App\Utils\Constants;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReviewRepository extends BaseRepository
{
    public function __construct(
        protected Book $modelBook
    ) {
        parent::__construct();
    }

    public function getModel(): string
    {
        return Review::class;
    }

    // change status
    public function updateStatus(int $id, int $status): bool
    {
        $review = $this->model->findOrFail($id);
        $review->status = $status;

        return $review->save();
    }
    public function getPaginateOnlyBook(int $bookId): LengthAwarePaginator
    {
        $perPage = Constants::DEFAULT_PER_PAGE;
        $relation = ['user'];
        $status = ReviewStatus::ACTIVE->value;

        return $this->model->where('book_id', $bookId)->where('status', $status)->with($relation)->orderBy('created_at', 'desc')->paginate($perPage);
    }
}
