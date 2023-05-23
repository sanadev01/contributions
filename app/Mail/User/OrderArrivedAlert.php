<?php

namespace App\Mail\User;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OrderArrivedAlert extends Mailable
{
    use Queueable, SerializesModels;
 
    public $order;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(Order $order)
    {
        Log::info('OrderArrivedAlert :__construct');
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Log::info('OrderArrivedAlert :builded');
        return $this->markdown('emails.user.order-arrived-alert')
        ->cc( config('hd.email.admin_email'));
    }
}
