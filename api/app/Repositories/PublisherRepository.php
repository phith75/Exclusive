<?php

namespace App\Repositories;

use App\Models\Book;
use App\Models\Publisher;

class PublisherRepository extends BaseRepository
{
    protected $model;

    public function getModel(): string
    {
        return Publisher::class;
    }
    public function inBook(int | array $id): bool
    {
        if (is_array($id)) {
            return Book::query()->whereIn('publisher_id', $id)->exists();
        }
        return Book::query()->where('publisher_id', $id)->exists();
    }
}
