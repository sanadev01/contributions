<?php
namespace App\Services\CorreosChile;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;



class ExportTxtChileManifestService
{
    private $container;
    private $clienteId;

    public function __construct($container)
    {
        $this->container = $container;
        $this->clienteId = config('correoschile.codeId');
    }

    public function handle()
    {
        $filename = $this->prepareTextFile();
        return $this->download($filename);
    }

    private function prepareTextFile()
    {
        $initial = '';
        $orders = $this->container->orders;

        $current_date = (Carbon::now())->toDateTimeString();
        $combine_date_time = str_replace(['-','', ' ',':'],'',$current_date); 
        $filename = $this->clienteId.'_'.$combine_date_time;
        
        try {
           
            Storage::put("manifests/txt/$filename.txt", $initial);
       } catch (\Exception $e) {
            return $e->getMessage();
       }
        $file = fopen("../storage/app/manifests/txt/$filename.txt", 'a');//opens file in append mode  
        foreach ($orders as $order)
        {
            $chile_response = json_decode($order->api_response);
            fwrite($file,  $this->combineChileResponseFields($chile_response));  
            fwrite($file, '|');
            fwrite($file, $this->container->awb);
            fwrite($file, '|');
            fwrite($file, $this->container->seal_no."\n");
        }
        fclose($file);
        return $filename;
    }

    private function combineChileResponseFields($chile_response)
    {
        return $chile_response->CodigoEncaminamiento.$chile_response->NumeroEnvio.'001';
    }

    public function download($filename)
    {
        $path = storage_path().'/'.'app'.'/manifests/txt/'.$filename.''.'.txt';
        
        if (file_exists($path)) {
            return Response::download($path);
        }
    
    }
}