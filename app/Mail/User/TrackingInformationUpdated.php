<?php

namespace App\Mail\User;

use App\Models\Tracking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrackingInformationUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $tracking;
    public $order;

    /**
     * Create a new message instance.
     *
     * @param Tracking $tracking
     */
    public function __construct(Tracking $tracking)
    {
        $this->tracking = $tracking;
        $this->order = $tracking->order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        app()->setLocale($this->order->user->preferredLocale());
        return $this->markdown('emails.user.tracking-information-updated')
            ->subject('Tracking information available')
            ->to(
                $this->tracking->order->user
            );
    }
}
