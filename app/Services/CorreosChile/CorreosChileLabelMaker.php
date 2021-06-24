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
        $chile_response = json_decode($this->order->chile_response);
        $description = $this->itemsDescription( $this->order->items);
        $date = \Carbon\Carbon::parse($this->order->updated_at)->format('d/m/Y H:i');
        $bar_code = $this->get_code_for_generating_barcode($chile_response);
        $clienteRemitente = config('correoschile.codeId');

        $pdf = \PDF::loadView('labels.chile.index', [
            'order' => $this->order,
            'chile_response' => $chile_response,
            'description' => $description,
            'date' => $date,
            'clienteRemitente' => $clienteRemitente,
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

    public function get_code_for_generating_barcode($chile_response)
    { 
        $bultos = '001';
        
        return $combine_code = $chile_response->CodigoEncaminamiento.$chile_response->NumeroEnvio.$bultos;

    }

}
