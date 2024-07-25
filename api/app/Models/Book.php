<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'thumbnail',
        'slug',
        'quantity',
        'description',
        'short_description',
        'borrowed_count',
        'category_id',
        'author_id',
        'publisher_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function bookImage(): HasMany
    {
        return $this->hasMany(BookImage::class);
    }

    public function lendTickets(): HasManyThrough
    {
        return $this->hasManyThrough(
            LendTicket::class,
            LendTicketDetail::class,
            'book_id',
            'id',
            'id',
            'lend_ticket_id'
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'book_id');
    }

    public function avgRating()
    {
        return $this->reviews()->avg('rating');
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $slug = Str::slug($value);

        $existingSlugCount = static::where('slug', 'like', $slug . '%')->count();

        if ($existingSlugCount > 0) {
            $this->attributes['slug'] = $slug . '-' . ($existingSlugCount + 1);
        } else {
            $this->attributes['slug'] = $slug;
        }
    }
}
