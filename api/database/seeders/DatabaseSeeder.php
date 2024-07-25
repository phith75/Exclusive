<?php

namespace Database\Seeders;

use App\Enum\Gender;
use App\Enum\UserRole;
use App\Enum\UserStatus;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookImage;
use App\Models\Category;
use App\Models\District;
use App\Models\LendTicket;
use App\Models\LendTicketDetail;
use App\Models\Province;
use App\Models\Publisher;
use App\Models\Review;
use App\Models\User;
use App\Models\Ward;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        Province::factory()->count(50)->create();
        District::factory()->count(200)->create();
        Ward::factory()->count(2000)->create();
        User::factory()->count(10)->create();

        LendTicket::factory()->count(100)->create();

        LendTicketDetail::factory()->count(200)->create();

        Review::factory()->count(100)->create();

        Wishlist::factory()->count(100)->create();


        User::create([
            'user_code' => 'U123456',
            'name' => 'Hoàng Phi',
            'email' => 'phith75@gmail.com',
            'password' => bcrypt('password'),
            'role' => UserRole::ADMIN->value,
            'gender' => Gender::MALE->value,
            'status' => UserStatus::ACTIVE->value,
        ]);
    }
}
