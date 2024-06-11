<?php

namespace App\Mail\User;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShipmentReady extends Mailable
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
        return $this->markdown('emails.user.shipment-ready')
                ->subject('Shipment Received on Warehouse. / Remessa recebida no armazém.')
                ->to($this->order->user);
    }
}
