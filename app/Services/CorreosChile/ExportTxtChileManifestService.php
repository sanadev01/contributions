<?php
namespace App\Services\CorreosChile;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;



class ExportTxtChileManifestService
{
    private $container;
    
    public function __construct($container)
    {
        $this->container = $container;

    }

    public function handle()
    {
        $this->prepareTextFile();
        return $this->download();
    }

    private function prepareTextFile()
    {
        $initial = '';
        $orders = $this->container->orders;
        try {
           
            Storage::put("manifests/txt/{$this->container->seal_no}_manifest.txt", $initial);
       } catch (\Exception $e) {
            return $e->getMessage();
       }
        $file = fopen("../storage/app/manifests/txt/{$this->container->seal_no}_manifest.txt", 'a');//opens file in append mode  
        foreach ($orders as $order)
        {
            $chile_response = json_decode($order->chile_response);
            fwrite($file,  $this->combineChileResponseFields($chile_response));  
            fwrite($file, '|');
            fwrite($file, $this->container->seal_no);
            fwrite($file, '|');
            fwrite($file, 'LS1293842224'."\n");

        }
        fclose($file);
    }

    private function combineChileResponseFields($chile_response)
    {
        return $chile_response->CodigoEncaminamiento.$chile_response->NumeroEnvio.'001';
    }

    public function download()
    {
        $filename = $this->container->seal_no.'_manifest.txt';
        $path = storage_path().'/'.'app'.'/manifests/txt/'.$filename;
        
        if (file_exists($path)) {
            return Response::download($path);
        }
    
    }
}