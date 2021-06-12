<?php
namespace App\Services\CorreosChile;

use App\Models\Order;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;


class CorreosChileLabelMaker
{
    private $order;

    public function setOrder($order)
    {
        $order = Order::with('recipient', 'items')->find($order->id);
        $this->order = $order;
    }

    public function saveLabel()
    {
        $chile_response = json_decode($this->order->chile_response);
        $description = $this->itemsDescription( $this->order->items);
        
         $pdf = \PDF::loadView('labels.chile.index', [
             'order' => $this->order,
             'chile_response' => $chile_response,
             'description' => $description,
         ]);
        
        Storage::put("labels/{$chile_response->NumeroEnvio}.pdf", $pdf->output());
    }
    
    public function getViewData()
    {
        $chile_response = json_decode($this->order->chile_response);
        $description = $this->itemsDescription( $this->order->items);

        return [
            'order' => $this->order,
            'chile_response' => $chile_response,
            'description' => $description,
            'barcodeNew' => new BarcodeGeneratorPNG(),
        ];
    }

    public function itemsDescription($items)
    {
        foreach($items as $item)
        {
            $itemDescription[] = $item->description;
        }

        $description = implode(" ", $itemDescription);

        return $description;
    }

}
