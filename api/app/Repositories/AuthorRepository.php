<?php

namespace App\Repositories;

use App\Models\Author;
use App\Models\Book;

class AuthorRepository extends BaseRepository
{
    protected $model;

    public function getModel(): string
    {
        return Author::class;
    }
    public function inBook(int | array $id): bool
    {
        if (is_array($id)) {
            return Book::query()->whereIn('author_id', $id)->exists();
        }
        return Book::query()->where('author_id', $id)->exists();
    }
}
