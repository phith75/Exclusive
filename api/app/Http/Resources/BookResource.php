<?php

namespace App\Http\Resources;

use App\Enum\ReviewStatus;
use App\Models\LendTicketDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lendTicketBookCount = LendTicketDetail::where('book_id', $this->id)
            ->where('returned_date', null)->count();
        $review = ReviewResource::collection($this->reviews)->where('status', ReviewStatus::ACTIVE->value);
        $avgRating = round($review->avg('rating'), 1);

        return [
            'id' => $this->id,
            'thumbnail' => $this->thumbnail,
            'name' => $this->name ?? '',
            'slug' => $this->slug,
            'category_id' => $this->category_id,
            'category_name' => $this->category->name ?? '',
            'author_id' => $this->author_id,
            'author_name' => $this->author->name ?? '',
            'publisher_id' => $this->publisher_id ?? null,
            'publisher_name' => $this->publisher->name ?? '',
            'quantity' => $this->quantity,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'borrowed_count' => $this->borrowed_count,
            'wishlists' => WishlistResource::collection($this->wishlists) ?? [],
            'reviews' => $review->toArray() ?? [],
            'avg_rating' => $avgRating ?? 0,
            'rating_count' => $review->count() ?? 0,
            'images' => BookImageResource::collection($this->bookImage) ?? [],
            'quantity_left' => $this->quantity - $lendTicketBookCount,
        ];
    }
}
