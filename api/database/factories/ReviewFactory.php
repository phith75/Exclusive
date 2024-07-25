<?php

namespace Database\Factories;

use App\Enum\ReviewStatus;
use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        $book = Book::inRandomOrder()->first();
        $user = User::inRandomOrder()->first();

        return [
            'book_id' => $book->id,
            'user_id' => $user->id,
            'book_name' => $book->name,
            'user_name' => $user->name,
            'description' => $this->faker->text(200),
            'rating' => $this->faker->numberBetween(1, 5),
            'status' => $this->faker->randomElement(ReviewStatus::cases())->value,
        ];
    }
}
