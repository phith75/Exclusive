<?php

namespace Database\Factories;

use App\Enum\LendTicketStatus;
use App\Models\LendTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LendTicketFactory extends Factory
{
    protected $model = LendTicket::class;

    public function definition()
    {
        // Generate a date within the current year
        $currentYear = date('Y');
        $startDate = "$currentYear-01-01";
        $endDate = "$currentYear-12-31";

        return [
            'lend_ticket_code' => $this->faker->unique()->randomNumber,
            'user_id' => User::inRandomOrder()->first()->id,
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'email' => $this->faker->safeEmail,
            'status' => $this->faker->randomElement(LendTicketStatus::cases())->value,
            'borrowed_date' => $this->faker->dateTimeBetween($startDate, $endDate),
            'phone' => $this->faker->phoneNumber,
            'note' => $this->faker->text,
        ];
    }
}
