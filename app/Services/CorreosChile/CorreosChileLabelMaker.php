<?php
namespace App\Services\CorreosChile;

use App\Models\Order;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;


class CorreosChileLabelMaker
{
    private $order;
    private $serviceType;
    private $normalization_code;
    private $customerId;

    public function setOrder($order)
    {
        $order = Order::with('recipient', 'items')->find($order->id);
        $this->order = $order;
    }

    public function saveLabel()
    {
        $chile_response = json_decode($this->order->chile_response);
        $description = $this->itemsDescription( $this->order->items);
        $date = \Carbon\Carbon::parse($this->order->updated_at)->format('d/m/Y H:i');
        $bar_code = $this->get_code_for_generating_barcode();
       
        $pdf = \PDF::loadView('labels.chile.index', [
            'order' => $this->order,
            'chile_response' => $chile_response,
            'description' => $description,
            'date' => $date,
            'barcodeNew' => new BarcodeGeneratorPNG(),
            'bar_code' => $bar_code,
        ]);
        
        Storage::put("labels/{$chile_response->NumeroEnvio}.pdf", $pdf->output());
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

    public function get_code_for_generating_barcode()
    {
        if($this->order->shipping_service_name == 'SRP'){
            $this->serviceType = 28;    //Product
        } else {
            $this->serviceType = 32;    //Product
        }

        $this->normalization_code = 1;  //Normalization Code
        $destination_postal_code = $this->order->recipient->zipcode; //Recepient Postal Code
        $fix_digit = 7;                 //Fix Number, always 7
        $this->customerId = '0002';       //Customer Id 
        $tracking_code  = $this->order->corrios_tracking_code;
        $second_fix_digit = '001';
        
       return $combine_code = $this->normalization_code.''.$this->serviceType.''.$destination_postal_code.''.$fix_digit.''.$this->customerId.''.$tracking_code.''.$second_fix_digit;

    }

}
