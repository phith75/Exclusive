<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ReviewResource extends JsonResource
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
            'book_id' => $this->book_id ?? '',
            'user_id' => $this->user_id ?? '',
            'user_name' => $this->user->name ?? '',
            'book_name' => $this->book->name ?? '',
            'description' => $this->description,
            'rating' => $this->rating,
            'status' => $this->status,
            'date' => $this->created_at->format('d F Y'),
        ];
    }
}
