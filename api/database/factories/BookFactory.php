<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'slug' => $this->faker->unique()->slug,
            'thumbnail' => $this->faker->imageUrl,
            'author_id' => Author::inRandomOrder()->first()->id,
            'publisher_id' => Publisher::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,
            'description' => $this->faker->paragraph,
            'short_description' => $this->faker->sentence(3),
            'borrowed_count' => $this->faker->numberBetween(0, 100),
            'quantity' => $this->faker->numberBetween(1, 20),
        ];
    }
}
