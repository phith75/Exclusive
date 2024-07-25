<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'slug',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
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
