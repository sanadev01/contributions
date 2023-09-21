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
        
        $this->order = $order;
    }

    public function saveLabel()
    {
        $chile_response = json_decode($this->order->api_response);
        $description = $this->itemsDescription( $this->order->items);
        $date = \Carbon\Carbon::parse($this->order->updated_at)->format('d/m/Y H:i');
        $bar_code = $this->get_code_for_generating_barcode($chile_response);
        $clienteRemitente = config('correoschile.codeId');
        $recipient_name = $this->getRecipientName();

        $pdf = \PDF::loadView('labels.chile.index', [
            'order' => $this->order,
            'chile_response' => $chile_response,
            'description' => $description,
            'date' => $date,
            'clienteRemitente' => $clienteRemitente,
            'barcodeNew' => new BarcodeGeneratorPNG(),
            'bar_code' => $bar_code,
            'recipient_name' => $recipient_name,
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

        if (strlen($description) > 40){
            $description = str_limit($description, 38);
        }
        
        return $description;
    }

    public function get_code_for_generating_barcode($chile_response)
    { 
        $bultos = '001';
        
        return $combine_code = $chile_response->CodigoEncaminamiento.$chile_response->NumeroEnvio.$bultos;

    }

    private function getRecipientName()
    {
        $full_name = $this->order->recipient->first_name. ' '. $this->order->recipient->last_name;
        
        if (strlen($full_name) > 25){
            $full_name = str_limit($full_name, 20);
        }

        return $full_name;
    }

}
