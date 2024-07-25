<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LendTicketResource extends JsonResource
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
            'lend_ticket_code' => $this->lend_ticket_code,
            'user_id' => $this->user_id ?? '',
            'name' => $this->name ?? '',
            'address' => $this->address,
            'email' => $this->email ?? '',
            'status' => $this->status,
            'borrowed_date' => $this->borrowed_date,
            'phone' => $this->phone ?? '',
            'note' => $this->note ?? '',
            'ticket_detail' => LendTicketDetailResource::collection($this->ticketDetail),
        ];
    }
}
