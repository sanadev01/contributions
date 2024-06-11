<?php

namespace App\Mail\User;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

class SendReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $content;

    /**
     * Create a new message instance.
     *
     * @return void
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
        app()->setLocale($this->order->user->preferredLocale());

        $cssToInlineStyles = new CssToInlineStyles();
        $css = file_get_contents(asset('app-assets/css/bootstrap.css'));
        $css .= file_get_contents(asset('app-assets/css/pages/invoice.css'));

        $order = $this->order;
        $html = view('admin.orders.receipt.index', compact('order'))->render();
        $this->content = $cssToInlineStyles->convert(
            $html,
            $css
        );

        return $this->markdown('emails.user.send-receipt')
            ->subject("Aqui está o seu recibo do pedido: {$order->shipment->whr_number}")
            ->to($order->user->email);
    }
}
