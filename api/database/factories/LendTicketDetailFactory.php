<?php

namespace Database\Factories;

use App\Enum\TicketDetailStatus;
use App\Models\Book;
use App\Models\LendTicket;
use App\Models\LendTicketDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class LendTicketDetailFactory extends Factory
{
    protected $model = LendTicketDetail::class;
    public function definition()
    {
        $lendTicket = LendTicket::inRandomOrder()->first();
        $borrowedDate = $lendTicket->borrowed_date;
        $borrowedDateTime = new \DateTime($borrowedDate);
        $startDate = $borrowedDateTime->format('Y-m-d');
        $endDate = $borrowedDateTime->modify('+10 days')->format('Y-m-d');
        $status = $this->faker->randomElement(TicketDetailStatus::cases())->value;

        $returnedDate = $status === 2 ? $this->faker->dateTimeBetween($startDate, $endDate) : null;
        $expectedRefundTime=  $this->faker->dateTimeBetween($startDate, $endDate);
        return [
            'lend_ticket_id' => $lendTicket->id,
            'book_id' => Book::inRandomOrder()->first()->id,
            'book_name' => $this->faker->sentence,
            'status' => $status,
            'expected_refund_time' => $expectedRefundTime,
            'returned_date' => $returnedDate,
        ];
    }
}
