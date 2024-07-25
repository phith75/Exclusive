<?php

namespace App\Repositories;

use App\Enum\TicketDetailStatus;
use App\Models\Book;
use App\Models\BookImage;
use App\Models\LendTicketDetail;
use Illuminate\Support\Collection;

class BookRepository extends BaseRepository
{
    protected $model;

    public function getModel(): string
    {
        return Book::class;
    }

    public function getBookImageByBookId(int $bookId): ?object
    {
        return BookImage::where('book_id', $bookId)->get();
    }

    // getMostBorrowed
    public function getMostBorrowed(int $limit): Collection
    {
        return $this->model->orderBy('borrowed_count', 'desc')->take($limit)->get();
    }
    public function getLendTicketDetailByBookId(int | array $bookId): bool
    {
        if (is_array($bookId)) {
            return LendTicketDetail::whereIn('book_id', $bookId)->whereIn('status', [TicketDetailStatus::BORROWED->value, TicketDetailStatus::OVERDUE->value, TicketDetailStatus::LOST->value])->exists();
        }

        return LendTicketDetail::where('book_id', $bookId)->whereIn('status', [TicketDetailStatus::BORROWED->value, TicketDetailStatus::OVERDUE->value, TicketDetailStatus::LOST->value])->exists();
    }
}
