<?php
namespace App\Services\CorreosChile;


use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderTracking;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;



class UploadChileManifestService
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
        $chile_upload_response = $this->upload($filename);

        if($chile_upload_response == true)
        {
            return $this->updateContainer();

        } else {
            return false;
        }
    }

    private function prepareTextFile()
    {
        $initial = '';
        $orders = $this->container->orders;

        $current_date = (Carbon::now())->toDateTimeString();
        $combine_date_time = str_replace(['-','', ' ',':'],'',$current_date); 
        $filename = $this->clienteId.'_'.$combine_date_time;
        
        try {

            Storage::put("manifests/uploads/$filename.txt", $initial);

       } catch (\Exception $e) {

            return $e->getMessage();
       }
        $file = fopen("../storage/app/manifests/uploads/$filename.txt", 'a');//opens file in append mode  
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

    public function upload($filename)
    {
        $content = Storage::get("manifests/uploads/$filename.txt");

        try {
            Storage::disk('correos-chile')->put("/entrada/aprocesar/$filename.txt", $content);
            return true;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function updateContainer()
    {
        $container = $this->container;
        $container->update([
            'response' => true,
        ]);

        $this->addOrderTracking();
        return true;
    }

    public function addOrderTracking()
    {
        $orders = $this->container->orders;

        foreach ($orders as $order)
        {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_SHIPPED,
                'type' => 'HD',
                'description' => 'Parcel transfered to airline',
                'country' => 'US',
                'city' => 'Miami'
            ]);
        }

        return true;
    }

}