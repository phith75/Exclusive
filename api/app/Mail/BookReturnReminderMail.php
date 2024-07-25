<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookReturnReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $bookTitles;
    public $dueDates;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param array $bookTitles
     * @param array $dueDates
     */
    public function __construct($user, array $bookTitles, array $dueDates)
    {
        $this->user = $user;
        $this->bookTitles = $bookTitles;
        $this->dueDates = $dueDates;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Book Return Reminder')
            ->view('emails.book_return_reminder')
            ->with([
                'user' => $this->user,
                'bookTitles' => $this->bookTitles,
                'dueDates' => $this->dueDates,
            ]);
    }
}
