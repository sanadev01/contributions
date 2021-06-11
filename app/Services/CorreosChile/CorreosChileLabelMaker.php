<?php
namespace App\Services\CorreosChile;

use App\Models\Order;
use Barryvdh\DomPDF\PDF;
use Picqer\Barcode\BarcodeGeneratorPNG;


class CorreosChileLabelMaker
{
    private $order;

    public function setOrder($order)
    {
        $order = Order::with('recipient', 'items')->find($order->id);
        $this->order = $order;
    }

    public function saveAs($path)
    {
        if ( !file_exists(dirname($path)) ){
            mkdir(dirname($path),0775,true);
        }

        $chile_response = json_decode($this->order->chile_response);
        $description = $this->itemsDescription( $this->order->items);

        return \PDF::loadView('labels.chile.index')->with([
            'order' => $this->order,
            'chile_response' => $chile_response,
            'description' => $description,
        ])->save($path);
    }

    public function render()
    {
        $chile_response = json_decode($this->order->chile_response);
        $description = $this->itemsDescription( $this->order->items);
        
        return view('labels.chile.index')->with([
            'order' => $this->order,
            'chile_response' => $chile_response,
            'description' => $description,
        ]);
       
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
