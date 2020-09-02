<?php

namespace App\Mail\User;

use App\Models\PreAlert;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ShipmentReady extends Mailable
{
    use Queueable, SerializesModels;

    public $preAlert;

    /**
     * Create a new message instance.
     *
     * @param PreAlert $preAlert
     */
    public function __construct(PreAlert $preAlert)
    {
        $this->preAlert = $preAlert;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        app()->setLocale($this->preAlert->user->preferredLocale());
        return $this->markdown('emails.user.shipment-ready')
                ->subject('Shipment Received on Warehouse.')
                ->to($this->preAlert->user);
    }
}
