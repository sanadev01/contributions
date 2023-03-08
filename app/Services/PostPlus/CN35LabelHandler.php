<?php

namespace App\Services\PostPlus;

use App\Services\PostPlus\PostPlusShipment;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Storage;

class CN35LabelHandler
{

    public static function handle(Container $container)
    {
        if (!$container->hasPostPlusService()) {
            return response()->json([ 'isSuccess' => false,  'message'  => "Only post plus container allowed!" ], 422);
        }

        if ($container->unit_response_list) {
            $cn35_base64 = json_decode($container->unit_response_list)->cn35;
            return response()->json(['isSuccess' => true, 'output'   => self::getLabelPath($container, $cn35_base64),'message'  => 'Label created successfully']);
        }

        $response =  (new PostPlusShipment($container))->getLabel();
        $data = $response->getData();

        if ($data->isSuccess) {
            return response()->json(['isSuccess' => true,'output'   => self::getLabelPath($container, $data->output), 'message'  => $data->message]);
        } else {
            return response()->json(['isSuccess' => false, 'message'  => $data->message], 422);
        }
    }

    public static function getLabelPath($container, $base64)
    {
        $path = storage_path("app/labels/{$container->unit_code}.pdf");

        if (!file_exists($path)) {
            Storage::put("labels/{$container->unit_code}.pdf", base64_decode($base64));
        }
        return $path;
    }
}
