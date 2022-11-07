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
    public $isAdmin;
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Request $request, $userData, $isAdmin)
    {
        $this->user = $user;
        $this->request = $request;
        $this->userData = $userData;
        $this->isAdmin = $isAdmin;
        $this->subject = $this->isAdmin ? 'Admin Setting Update' : 'User Setting Update';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.admin.setting-update')
        ->to(
            config('hd.email.admin_email'),
            config('hd.email.admin_name')
        )->cc('mnaveedsaim@gmail.com')
            ->subject($this->subject);
    }
}
