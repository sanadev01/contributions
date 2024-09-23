<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorCode extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->view('email.auth.two_factor_code')->with([
            'token' => $this->token,
        ]);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
}
