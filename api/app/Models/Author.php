<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Author extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($author) {
            $slug = Str::slug($author->name);
            $existingSlugCount = static::where('slug', 'like', $slug . '%')->onlyTrashed()->exists();
            if ($existingSlugCount) {
                $author->slug = $slug . '-' . 1;
            } else {
                $author->slug = $slug;
            }
        });

        static::updating(function ($author) {
            if ($author->isDirty('name')) {
                $slug = Str::slug($author->name);

                $existingSlugCount = static::where('slug', 'like', $slug . '%')->onlyTrashed()->exists();

                if ($existingSlugCount) {
                    $author->slug = $slug . '-' . 1; 
                } else {
                    $author->slug = $slug;
                }
            }
        });
    }
}
