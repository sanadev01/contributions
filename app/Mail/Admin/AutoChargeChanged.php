<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class AutoChargeChanged extends Mailable
{
    use Queueable, SerializesModels; 
    public $cardNo;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    { 
        $this->user = Auth::user();
         $this->cardNo = "**** **** **** ". substr(optional(auth()->user()->billingInformations->where('id',setting('charge_biling_information', null,auth()->id()))->first())->card_no??"****" ,-4);

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.admin.auto-charge-change')
        ->to(
            config('hd.email.admin_email'),
            config('hd.email.admin_name'),
        )->cc('mnaveedsaim@gmail.com')
        ->subject('Auto Charge Settings');
        
    }
}
