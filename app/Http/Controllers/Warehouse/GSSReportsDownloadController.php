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
        if ($response->successful()) {
            $pdfContent = $response->body();
            $filename = "$report.pdf"; 
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            return response()->stream(
                function () use ($pdfContent) {
                    echo $pdfContent;
                },
                200,
                $headers
            );
        } else {
            $data= json_decode($response);
            session()->flash('alert-danger', $data->message);
            return back();
        }
    }
}
