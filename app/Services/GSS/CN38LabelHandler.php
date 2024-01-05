<?php

namespace App\Services\GSS;

use Barryvdh\DomPDF\PDF;
use App\Models\Warehouse\Container;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Support\Facades\Storage;
use App\Services\GSS\GSSShipment;

class CN38LabelHandler
{

    public static function handle(DeliveryBill $deliveryBill)
    {
        $container = $deliveryBill->containers->first();

        $shipment = json_decode($container->unit_response_list)->cn35;
        if($shipment->id) {
            $updateShipment = (new GSSShipment($container))->getShipmentDetails($shipment->id);
            $shipmentDetails = $updateShipment->getData();
            $containers = Container::where('awb', $container->awb)->get();
            foreach($containers as $package) {
                $package->update([
                    'unit_response_list' => json_encode(['cn35'=>$shipmentDetails->output]),
                    'unit_code' => $shipmentDetails->output->bags[0]->outboundBagNrs,
                ]); 
            }
        }

        $updatedResponse = json_decode($container->unit_response_list)->cn35;
        $response = (new GSSShipment($container))->getLabel($updatedResponse->documents[0]->id);

        $streamFileData = $response->getBody()->getContents();
        $fileName = "{$container->awb}-CN38.pdf";
        $headers = [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
        ];

        $label = response( $streamFileData, 200, $headers );
        Storage::put("labels/{$fileName}", $label);
        return $label;
    }
}
