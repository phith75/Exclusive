<?php

namespace App\Models;

use App\Enum\LendTicketStatus;
use App\Enum\TicketDetailStatus;
use App\Repositories\LendTicketRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LendTicketDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'lend_ticket_id',
        'book_id',
        'book_name',
        'quantity',
        'status',
        'expected_refund_time',
        'returned_date',
    ];

    public function lendTicket(): BelongsTo
    {
        return $this->belongsTo(LendTicket::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public static function boot(): void
    {
        static::updating(function ($model) {
            $checkStatus = LendTicketDetail::where('lend_ticket_id', $model->lend_ticket_id)->get();
            $allReturned = 0;
            foreach ($checkStatus as $status) {
                if ($status->status != TicketDetailStatus::RETURNED->value) {
                    $allReturned++;
                }
            }

            if ($allReturned == 1) {
                $lendTicketRepo = new LendTicketRepository();
                $lendTicketRepo->updateTicketStatus($model->lend_ticket_id, LendTicketStatus::RETURNED->value);
            }
        });

        parent::boot();
    }
}
