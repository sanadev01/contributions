<?php

namespace App\Mail\User;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use App\Services\Excel\Export\ScanOrderExport;

class Shipment extends Mailable
{
    use Queueable, SerializesModels;

    public $orders;
    public $user;
    public $filePath;

    /**
     * Create a new message instance.
     *
     * @param PreAlert $preAlert
     */
    public function __construct(Collection $orders)
    { 
        $this->orders = $orders;
        $this->user = $orders->first()->user;        
        $exportService = new ScanOrderExport($orders);
        $this->filePath =  $exportService->getFilePath();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        app()->setLocale($this->user->locale);
        return $this->markdown('emails.user.shipment')
                ->subject('Order Update Alert')
                ->to($this->user)
                ->attach($this->filePath)
                ->cc(
                    config('hd.email.admin_email'),
                    config('hd.email.admin_name')
                );
    }
}
