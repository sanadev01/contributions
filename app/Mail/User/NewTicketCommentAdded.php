<?php

namespace App\Mail\User;

use App\Models\TicketComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTicketCommentAdded extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var TicketComment
     */
    public $ticketComment;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(TicketComment $ticketComment)
    {
        $this->ticketComment = $ticketComment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        app()->setLocale($this->ticketComment->ticket->user->locale);
        return $this->markdown('emails.user.new-ticket-comment-added')
            ->subject('New Ticket Comment Added')
            ->to($this->ticketComment->ticket->user)
            ->bcc(
                config('hd.email.admin_email'),
                config('hd.email.admin_name')
            );
    }
}
