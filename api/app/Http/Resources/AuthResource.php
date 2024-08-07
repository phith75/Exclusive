<?php

namespace App\Http\Resources;

use App\Enum\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
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
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'num_borrowed_books' => $this->num_borrowed_books,
            'google_id' => $this->google_id,
            'role' => $this->role,
            'role_name' => UserRole::from($this->role)->label(),
            'status' => $this->status,
            'province_id' => $this->province_id ?? null,
            'province_name' => $this->province->name ?? null,
            'district_id' => $this->district_id ?? null,
            'district_name' => $this->district->name ?? null,
            'ward_id' => $this->ward_id ?? null,
            'ward_name' => $this->ward->name ?? null,
            'wishlist_count' => $this->wishlists->count(),
        ];
    }
}
