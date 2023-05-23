<?php

namespace App\Mail\User;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Shipment extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     *
     * @param PreAlert $preAlert
     */
    public function __construct(Order $order)
    {
        \Log::info('Shipment');
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        app()->setLocale($this->order->user->locale);
        return $this->markdown('emails.user.shipment')
                ->subject('Order Update Alert')
                ->to($this->order->user);
    }
}
