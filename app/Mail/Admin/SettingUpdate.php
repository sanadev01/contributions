<?php

namespace App\Mail\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class SettingUpdate extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $request;
    public $userData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Request $request, $userData)
    {
        $this->user = $user;
        $this->request = $request;
        $this->userData = $userData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.admin.setting-update')
            ->to('mnaveedsaim@gmail.com')
            ->subject('Setting Update');;
    }
}
