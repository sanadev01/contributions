<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class AutoChargeChanged extends Mailable
{
    use Queueable, SerializesModels; 
    public $user;
    public $oldData;
    public $newData;
    public $selectedCard;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($oldData,$newData)
    { 
       $this->user = Auth::user();
       $this->oldData = $oldData;
       $this->newData = $newData;
       $this->selectedCard = $this->user->billingInformations->where('id',  setting('charge_biling_information', null, $this->user->id))->first();

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
           $this->user->email,
           $this->user->full_name,
        )->cc([ config('hd.email.admin_email'), 'mnaveedsaim@gmail.com'])
        ->subject('Auto Charge Settings');   
    }
}
