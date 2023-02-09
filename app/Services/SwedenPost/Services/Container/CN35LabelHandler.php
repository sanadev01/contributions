<?php

namespace App\Services\SwedenPost\Services\Container;

use App\Services\SwedenPost\Services\Container\DirectLinkReceptacle;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Storage;

class CN35LabelHandler
{

    public static function handle(Container $container)
    {
        if (!$container->hasSwedenPostService()) {
            return response()->json([ 'isSuccess' => false,  'message'  => "Only sweden container allowed!" ], 422);
        }
        
        if($container->id == 3005) {
            $cn35_base64 = json_decode($container->unit_response_list)->cn35;
            \Log::info('CN35 Base64');
            \Log::info($cn35_base64);
            return response()->json(['isSuccess' => true, 'output'   => self::getLabelPath($container, $cn35_base64),'message'  => 'Label created successfully']);
        }

        if ($container->unit_response_list) {
            $cn35_base64 = json_decode($container->unit_response_list)->cn35;
            return response()->json(['isSuccess' => true, 'output'   => self::getLabelPath($container, $cn35_base64),'message'  => 'Label created successfully']);
        }

        $response =  (new DirectLinkReceptacle($container))->getLabel();
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
