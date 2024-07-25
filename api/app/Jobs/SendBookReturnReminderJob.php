<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Mail\BookReturnReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendBookReturnReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $bookTitles;
    protected $dueDates;

    /**
     * Create a new job instance.
     */
    public function __construct($user, array $bookTitles, array $dueDates)
    {
        $this->user = $user;
        $this->bookTitles = $bookTitles;
        $this->dueDates = $dueDates;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info($this->user['email']);
        Mail::to($this->user['email'])->send(new BookReturnReminderMail($this->user, $this->bookTitles, $this->dueDates));
    }
}
