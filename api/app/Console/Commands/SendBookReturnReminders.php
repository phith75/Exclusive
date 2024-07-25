<?php

namespace App\Console\Commands;

use App\Enum\TicketDetailStatus;
use App\Jobs\SendBookReturnReminderJob;
use App\Models\LendTicketDetail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendBookReturnReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:book-return-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to customers about returning books';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dueDate = Carbon::now()->addDays(1)->format('Y-m-d');
        $books = LendTicketDetail::where('expected_refund_time', '<=', $dueDate)
            ->whereNull('returned_date')
            ->with(['lendTicket.user', 'book'])
            ->get();

        // Update status for all books to OVERDUE
        $books->each(function ($bookDetail) {
            $bookDetail->update(['status' => TicketDetailStatus::OVERDUE->value]);
        });

        // Group books by user email
        $booksGroupedByEmail = $books->groupBy(function ($bookDetail) {
            return $bookDetail->lendTicket->user->email;
        });
        $this->info('book return reminders: ' . count($booksGroupedByEmail));
        // Dispatch job for each user
        foreach ($booksGroupedByEmail as $email => $books) {
            $user = $books->first()->lendTicket->user;
            $bookTitles = $books->pluck('book.name')->toArray();
            $dueDates = $books->pluck('expected_refund_time')->toArray();
            $this->info('sending book return reminders: email: ' . $user->email . ', books: ' . count($bookTitles) . ', due date: ' . $dueDates[0]);
            SendBookReturnReminderJob::dispatch($user, $bookTitles, $dueDates);
        }

        return 0;
    }
}
