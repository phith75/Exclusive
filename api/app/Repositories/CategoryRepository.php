<?php

namespace App\Repositories;

use App\Models\Book;
use App\Models\Category;

class CategoryRepository extends BaseRepository
{
    protected $model;

    public function getModel(): string
    {
        return Category::class;
    }
    public function inBook(int | array $id): bool
    {
        if (is_array($id)) {
            return Book::query()->whereIn('category_id', $id)->exists();
        }
        return Book::query()->where('category_id', $id)->exists();
    }
}
