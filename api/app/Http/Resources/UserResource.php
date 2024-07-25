<?php

namespace App\Http\Resources;

use App\Enum\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'user_code' => $this->user_code,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'google_id' => $this->google_id,
            'role' => $this->role,
            'role_name' => UserRole::from($this->role)->label(),
            'address' => $this->address ?? '',
            'phone' => $this->phone ?? '',
            'date_of_birth' => $this->date_of_birth ?? '',
            'gender' => $this->gender,
            'num_borrowed_books' => $this->num_borrowed_books,
            'province_id' => $this->province_id,
            'province_name' => $this->province->name ?? '',
            'district_id' => $this->district_id,
            'district_name' => $this->district->name ?? '',
            'ward_id' => $this->ward_id,
            'ward_name' => $this->ward->name ?? '',
            'wishlists' => WishlistResource::collection($this->wishlists),
            'reviews' => ReviewResource::collection($this->reviews),
        ];
    }
}
