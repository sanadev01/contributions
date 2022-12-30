<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class AutoCharge extends Mailable
{
    use Queueable, SerializesModels;
    public $charge;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($charge, $user)
    {
        $this->charge = $charge;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.admin.auto-charge')
        ->to(
            config('hd.email.admin_email'),
            config('hd.email.admin_name'),
        )->cc('mnaveedsaim@gmail.com')
        ->subject('Auto Charge Notification');
        
    }
}
