<?php
namespace App\Services\Anjun\Services;
use App\Models\Order;
use App\Services\Anjun\Services\ReceiverInfo;
use App\Services\Anjun\Services\SenderInfo;
class Package
{
    public $receiver;
    public $invoices = [];
    public $sender;
    public $order; 
    public function __construct(Order $order)
    {
        $this->order = $order;

        foreach ($order->items as $orderItem) {
            $this->invoices[] = (new InvoiceInfo($orderItem));
        }
        $this->receiver = (new ReceiverInfo($order->recipient)); 
        $this->sender   = (new SenderInfo($order));
    }

    public function requestBody()
    {
        $invoiceInfo = [];
        foreach ($this->invoices as $invoice) {
            $invoiceInfo[] = $invoice->requestBody();
        }
        $receiverInfo = $this->receiver->requestBody();
        $senderInfo = $this->sender->requestBody();
         
        return [
            "customerChannelId" => 974,
            "orderType" => 1,
            "currency" => "USD",
            "orderNumber" => "PHFCESHI124ZDZX".$this->order->id."A",
            "hasBack" => 0,
            "packageType" => "goods",
            "prepaymentVat" => "other",
            "deliveryTerms" => "",
            "vat" => "",
            "invoiceInfo" => $invoiceInfo,
            "receiverInfo" => $receiverInfo,
            "senderInfo" => $senderInfo
        ];
    }
}
