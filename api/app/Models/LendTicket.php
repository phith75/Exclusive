<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LendTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'status',
        'lend_ticket_code',
        'borrowed_date',
        'note',
        'address',
        'phone',
        'email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticketDetail(): HasMany
    {
        return $this->hasMany(LendTicketDetail::class);
    }

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, LendTicketDetail::class, 'lend_ticket_id', 'book_id');
    }

    public static function boot(): void
    {
        static::creating(function ($model) {
            $model->lend_ticket_code = 'LTKIAI' . strtoupper(uniqid());
            $user = User::find($model->user_id);
            $model->name = $user->name;
            $model->email = $user->email;
        });
        parent::boot();
    }
}
