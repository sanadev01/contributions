<?php

namespace App\Mail\Admin;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PreAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $order)
    {
        $this->order = $order;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //dd($this->message);
        return $this->markdown('emails.admin.pre-alert')->with(['message' => $this->message])->to('malikasimit@gmail.com')->subject('Pre Alert HD-BR');
    }
}
