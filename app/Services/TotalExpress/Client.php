<?php

namespace App\Services\TotalExpress;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Http;
use App\Models\Warehouse\DeliveryBill;
use App\Services\TotalExpress\Services\Parcel;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Converters\UnitsConverter;
use App\Services\Correios\Contracts\Package;
use App\Services\Correios\Models\PackageError;

class Client
{

    protected $email;
    protected $password;
    protected $baseUrl;
    protected $token;
    protected $client;

    public function __construct()
    {
        if (app()->isProduction()) {
            $this->email = config('total_express.production.email');
            $this->password = config('total_express.production.password');
            $this->baseUrl = config('total_express.production.baseUrl');
        } else {
            $this->email = config('total_express.test.email');
            $this->password = config('total_express.test.password');
            $this->baseUrl = config('total_express.test.baseUrl');
        }




        $this->client = new GuzzleClient();
        $authParams = [
            'email' => $this->email,
            'password' => $this->password
        ];
        $response = $this->client->post("$this->baseUrl/authenticate/nobordist/seller", ['json' => $authParams]);
        $data = json_decode($response->getBody()->getContents());
        if ($data->auth_token) {
            $this->token = $data->auth_token;
        }
    }

    private function getHeaders()
    {
        return [
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/json'
        ];
    }

    public function createPackage(Order $order)
    {
        $shippingRequest = (new Parcel($order))->getRequestBody();

        try {
            $request = Http::withHeaders($this->getHeaders())->post("$this->baseUrl/v1/orders", $shippingRequest);
            $response = json_decode($request); 
            if ($response->status=="SUCCESS" && $response->id) {
                $getLabel = Http::withHeaders($this->getHeaders())->get("$this->baseUrl/v1/orders/$response->id/cn23-merged");
                $getLabelResponse = json_decode($getLabel);
                if($getLabel->status=="SUCCESS") {
                    $mergedResponse = [
                        'orderResponse' => $request,
                        'labelResponse' => $getLabel,
                    ];
                    $order->update([
                        'corrios_tracking_code' => $response->reference,
                        'api_response' => json_encode($mergedResponse),
                        'cn23' => [
                            "tracking_code" => $response->reference,
                            "stamp_url" => route('warehouse.cn23.download',$order->id),
                            'leve' => false
                        ],
                    ]);
                    // store order status in order tracking
                    return $this->addOrderTracking($order);
                }
                if($getLabel->status=="ERROR") {
                    return new PackageError(new HandleError($request));
                }
            } else {
                return new PackageError(new HandleError($request));
            }
        } catch (\Exception $exception) {
            return new PackageError($exception->getMessage());
        }
    }

    public function addOrderTracking($order)
    {
        if ($order->trackings->isEmpty()) {
            OrderTracking::create([
                'order_id' => $order->id,
                'status_code' => Order::STATUS_PAYMENT_DONE,
                'type' => 'HD',
                'description' => 'Order Placed',
                'country' => ($order->user->country != null) ? $order->user->country->code : 'US',
                'city' => 'Miami',
            ]);
        }

        return true;
    }

    public function createReceptacle($container)
    {
        $containers = Container::where('awb', $container->awb)->get();
        $url = "$this->baseUrl/Receptacle/CreateReceptacleForRateTypeToDestination";
        $weight = 0;
        $piecesCount = 0;
        if ($container->services_subclass_code == ShippingService::TotalExpress_IPA) {
            $rateType = "IPA";
            $foreignOECode = "CWB";
        } elseif ($container->services_subclass_code == ShippingService::TotalExpress_EPMEI) {
            $rateType = 'EPMEI';
            $foreignOECode = "SAO";
        } elseif ($container->services_subclass_code == ShippingService::TotalExpress_EPMI) {
            $rateType = 'EPMI';
            $foreignOECode = "RIO";
        } elseif ($container->services_subclass_code == ShippingService::TotalExpress_EFCM) {
            $rateType = 'EFCM';
            $foreignOECode = "CWB";
        }
        if ($containers[0]->awb) {
            foreach ($containers as $package) {
                $weight += UnitsConverter::kgToPound($package->getWeight());
                $piecesCount = $package->getPiecesCount();
            }
            $body = [
                "rateType" => $rateType,
                "dutiable" => true,
                "receptacleType" => "E",
                "foreignOECode" => $foreignOECode,
                "countryCode" => "BR",
                "dateOfMailing" => Carbon::now(),
                "pieceCount" => $piecesCount,
                "weightInLbs" => $weight,
            ];
            $response = Http::withHeaders($this->getHeaders())->post($url, $body);
            $data = json_decode($response);

            if ($response->successful() && $data->success == true) {

                return $this->addPackagesToReceptacle($data->receptacleID, $containers);
            } else {
                return $this->responseUnprocessable($data->message);
            }
        } else {
            return $this->responseUnprocessable("Airway Bill Number is Required for Processing.");
        }
    }

    public function addPackagesToReceptacle($id, $containers)
    {
        $codes = [];
        foreach ($containers as $key => $container) {
            foreach ($container->orders as $key => $item) {
                $codesToPush = [
                    $item->corrios_tracking_code,
                ];
                array_push($codes, $codesToPush);
            }
        }
        $parcels = implode(",", array_merge(...$codes));
        $url = $this->baseUrl . '/Package/AddPackagesToReceptacle';
        $body = [
            "uspsPackageID" =>  $parcels,
            "receptacleID" => $id,
        ];
        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        $data = json_decode($response);
        if ($response->successful() && $data->success == true) {
            return $this->moveReceptacleToOpenDispatch($id, $containers);
        } else {
            return $this->responseUnprocessable($data->message);
        }
    }

    public function moveReceptacleToOpenDispatch($id, $containers)
    {
        $url = $this->baseUrl . "/Receptacle/MoveReceptacleToOpenDispatch/$id";
        $response = Http::withHeaders($this->getHeaders())->get($url);
        $data = json_decode($response);
        if ($response->successful() && $data->success == true) {
            return $this->closeDispatch($id, $containers);
        } else {
            return $this->responseUnprocessable($data->message);
        }
    }

    public function closeDispatch($id, $containers)
    {
        $url = $this->baseUrl . '/Dispatch/CloseDispatch';
        $body = [
            "departureDateTime" => Carbon::now(),
            "arrivalDateTime" => Carbon::now()->addDays(1),
        ];
        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        $data = json_decode($response);
        if ($response->successful() && $data->success == true) {
            return $this->getReceptacleLabel($id, $containers, $data->dispatchID);
        } else {
            return $this->responseUnprocessable($data->message);
        }
    }

    public function getReceptacleLabel($id, $containers, $dispatchID)
    {

        $url = $this->baseUrl . '/Receptacle/GetReceptacleLabel';
        $body = [
            "receptacleID" =>  $id,
            "labelFormat" => "PDF",
        ];
        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        $data = json_decode($response);
        $reportsUrl = $this->baseUrl . "/Dispatch/GetRequiredReportsForDispatch/$dispatchID";
        $reportsResponse = Http::withHeaders($this->getHeaders())->get($reportsUrl);
        $reportData = json_decode($reportsResponse);
        if ($response->successful() && $data->success == true) {
            foreach ($containers as $package) {
                $package->update([
                    'unit_response_list' => json_encode(['cn35' => $data, 'manifest' => $reportData, 'dispatchID' => $dispatchID]),
                    'unit_code' => $id,
                    'response' => 1
                ]);
            }
            return $this->responseSuccessful($data, 'Container registration is successfull. You can download CN35 label');
        } else {
            return $this->responseUnprocessable($data->message);
        }
    }

    public function generateDispatchReport($report, $dispatchID)
    {

        $url = $this->baseUrl . '/Dispatch/GenerateDispatchReport';
        $body = [
            "dispatchID" => $dispatchID,
            "reportID" => $report,
            "permitNumber" => '',
        ];
        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        $data = json_decode($response);
        if ($response->successful() && $data->success == true) {
            return $this->responseSuccessful($data, 'File Exists');
        } else {
            return $this->responseUnprocessable($data->message);
        }
    }

    public static function responseUnprocessable($message)
    {
        return response()->json([
            'isSuccess' => false,
            'message' => $message,
        ], 422);
    }
    public static function responseSuccessful($output, $message)
    {
        return response()->json([
            'isSuccess' => true,
            'output' => $output,
            'message' =>  $message,
        ]);
    }
}
