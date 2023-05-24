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
    public $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->user = $order->user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.user.order-arrived-alert')
                    ->to($this->user->email, $this->user->name)
                    ->cc(config('hd.email.admin_email'))
                    ->subject('Order Update Alert');
    }
}
