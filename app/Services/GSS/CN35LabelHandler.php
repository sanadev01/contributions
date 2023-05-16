<?php

namespace App\Services\GSS;

use App\Models\Warehouse\Container;

class CN35LabelHandler
{

    public static function handle(Container $container)
    {
        if (!$container->hasGSSService()) {
            return response()->json([ 'isSuccess' => false,  'message'  => "Only GSS container allowed!" ], 422);
        }

        $cn35Label = json_decode($container->unit_response_list)->cn35->labels[0];

        $cn35PDF = base64_decode($cn35Label);
        $path = storage_path("{$container->unit_code}.pdf");
        file_put_contents($path, $cn35PDF); //Temp File
        return response()->download($path)->deleteFileAfterSend(true); //Delete File
    }

}
