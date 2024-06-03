<?php

namespace App\Services\PostPlus;

use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Storage;
use App\Services\PostPlus\PostPlusShipment;

class CN35LabelHandler
{

    public static function handle(Container $container, $requestId)
    {
        if (!$container->has_post_plus_service) {
            return response()->json(['isSuccess' => false,  'message'  => "Only post plus container allowed!"], 422);
        }

        $shipment = json_decode($container->unit_response_list)->cn35;
        if ($shipment->id) {
            //Check for Documents
            $containers = Container::where('awb', $container->awb)->get();
            $updateShipment = (new PostPlusShipment($container))->getShipmentDetails($shipment->id);
            $shipmentDetails = $updateShipment->getData();
            foreach ($containers as $package) {
                $package->update([
                    'unit_response_list' => json_encode(['cn35' => $shipmentDetails->output]),
                    'unit_code' => $shipmentDetails->output->bags[0]->outboundBagNrs,
                ]);
            }
        }

        $updatedResponse = json_decode($container->unit_response_list)->cn35;
        if ($updatedResponse->documents) {

            $response = (new PostPlusShipment($container))->getLabel($updatedResponse->documents[$requestId]->id);
            $streamFileData = $response->getBody()->getContents();
            if ($requestId == 3) {
                $fileName = "{$container->awb}-CN31.pdf";
            } else if ($requestId == 2) {
                $fileName = "{$container->awb}-CN33.pdf";
            } else {
                $fileName = "{$container->unit_code}-CN35.pdf";
            }
            $headers = [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename=' . $fileName,
            ];

            $label = response($streamFileData, 200, $headers);
            Storage::put("labels/{$fileName}", $label);
            return $label;
        } else {
            session()->flash('alert-danger', 'Unable to retrieve documents from Post Plus');
            return back();
        }
    }
}
