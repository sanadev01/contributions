<?php
namespace App\Services\Anjun\Services;
use App\Models\Order;
use App\Models\ShippingService;
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
            $this->invoices[] = (new InvoiceInfo($orderItem,$order));
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

        return ([
            "customerChannelId" => $this->order->shippingService->service_sub_class == ShippingService::AJ_Express_CN ? '1905':'1906',
            "orderType" => 1,
            "currency" => "USD",
            "orderNumber" => "PHFCESHI126ZDZX".$this->order->id,
            "hasBack" => 0,
            "packageType" => "goods",
            "prepaymentVat" => "other", 
            "deliveryTerms" => $this->order->tax_modality == 'ddp'||$this->order->tax_modality == 'DDP'?"DDP":'DDU',

            "vat" => "",
            "invoiceInfo" => $invoiceInfo,
            "receiverInfo" => $receiverInfo,
            "senderInfo" => $senderInfo
        ]);
    }
}
