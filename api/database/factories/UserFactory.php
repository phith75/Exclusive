<?php

namespace Database\Factories;

use App\Enum\Gender;
use App\Enum\UserRole;
use App\Models\District;
use App\Models\Province;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'user_code' => $this->faker->unique()->bothify('U######'),
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'),
            'address' => $this->faker->address,
            'date_of_birth' => $this->faker->date(),
            'gender' => $this->faker->randomElement([Gender::MALE, Gender::FEMALE, Gender::OTHER])->value,
            'num_borrowed_books' => $this->faker->numberBetween(0, 10),
            'google_id' => $this->faker->unique()->uuid,
            'role' => UserRole::USER->value,
            'status' => $this->faker->numberBetween(1, 2),
            'province_id' => Province::inRandomOrder()->first()->id,
            'district_id' => District::inRandomOrder()->first()->id,
            'ward_id' => Ward::inRandomOrder()->first()->id,
        ];
    }
}
