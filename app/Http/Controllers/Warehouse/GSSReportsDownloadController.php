<?php

namespace App\Http\Controllers\Warehouse;

use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use App\Models\Warehouse\DeliveryBill;
use Illuminate\Support\Facades\Storage;
use App\Services\GSS\Client;

class GSSReportsDownloadController extends Controller
{
    public function __invoke($report, $dispatchID)
    {
        $client = new Client();
        $response =  $client->generateDispatchReport($report, $dispatchID);
        $data = $response->getData(); 
        if($data->isSuccess){
            $pdfReport = base64_decode($data->output->report);
            $path = storage_path("{$report}.pdf");
            file_put_contents($path, $pdfReport); //Temp File
            return response()->download($path)->deleteFileAfterSend(true); //Delete File
        } 
        else{
            session()->flash('alert-danger', $data->message);
            return back();
        }
    }
}
