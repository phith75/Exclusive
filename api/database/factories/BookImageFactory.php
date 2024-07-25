<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookImageFactory extends Factory
{
    protected $model = BookImage::class;

    public function definition()
    {
        return [
            'book_id' => Book::inRandomOrder()->first()->id,
            'image' => $this->faker->imageUrl,
        ];
    }
}
