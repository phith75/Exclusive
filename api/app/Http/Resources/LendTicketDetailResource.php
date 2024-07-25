<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LendTicketDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lend_ticket_id' => $this->lend_ticket_id,
            'book_id' => $this->book_id ?? '',
            'quantity' => $this->quantity,
            'book_name' => $this->book_name ?? '',
            'book_slug' => $this->book->slug ?? '',
            'status' => $this->status,
            'expected_refund_time' => $this->expected_refund_time,
            'returned_date' => $this->returned_date,
        ];
    }
}
