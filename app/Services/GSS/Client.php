<?php

namespace App\Services\GSS;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\ZoneCountry;
use App\Models\OrderTracking;
use App\Models\ShippingService;
use App\Models\Warehouse\Container;
use Illuminate\Support\Facades\Http;
use App\Models\Warehouse\DeliveryBill;
use App\Services\GSS\Services\Parcel;
use GuzzleHttp\Client as GuzzleClient;
use App\Services\Converters\UnitsConverter;
use App\Services\Correios\Contracts\Package;
use App\Services\Correios\Models\PackageError;

class Client
{

    protected $userId;
    protected $password;
    protected $locationId;
    protected $workStationId;
    protected $baseUrl;
    protected $token;
    protected $gssProfit;
    protected $client;

    public function __construct()
    {
        if (app()->isProduction()) {
            $this->userId = config('gss.production.userId');
            $this->password = config('gss.production.password');
            $this->locationId = config('gss.production.locationId');
            $this->workStationId = config('gss.production.workStationId');
            $this->baseUrl = config('gss.production.baseUrl');
        } else {
            $this->userId = config('gss.test.userId');
            $this->password = config('gss.test.password');
            $this->locationId = config('gss.test.locationId');
            $this->workStationId = config('gss.test.workStationId');
            $this->baseUrl = config('gss.test.baseUrl');
        }

        $this->client = new GuzzleClient();
        $authParams = [
            'userId' => $this->userId,
            'password' => $this->password,
            'locationId' => $this->locationId,
            'workStationId' => $this->workStationId,
        ];
        $response = $this->client->post("$this->baseUrl/Authentication/login", ['json' => $authParams]);
        $data = json_decode($response->getBody()->getContents());
        if ($data->accessToken) {
            $this->token = $data->accessToken;
        }

        $this->gssProfit = '';
    }

    private function getHeaders()
    {
        return [
            'Authorization' => "Bearer {$this->token}",
            'Accept' => 'application/json'
        ];
    }

    public function createPackage(Package $order)
    {
        $shippingRequest = (new Parcel())->getRequestBody($order);
        try {
            $request = Http::withHeaders($this->getHeaders())->post("$this->baseUrl/Package/LabelAndProcessPackage", $shippingRequest);
            $response = json_decode($request);
            if($response->success) {
                $order->update([
                    'corrios_tracking_code' => $response->trackingNumber,
                    'api_response' => $request,
                    'cn23' => [
                        "tracking_code" => $response->trackingNumber,
                        "stamp_url" => route('warehouse.cn23.download', $order->id),
                        'leve' => false
                    ],
                ]);
                // store order status in order tracking
                return $this->addOrderTracking($order);
            } else {
                return new PackageError("Error while creating parcel. <br> Error Code: " . $response->statusCode . ". <br> Error Description: " . $response->message);
            }
            return null;
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
        // $containers = Container::where('awb', $container->awb)->get();
        $url = "$this->baseUrl/Receptacle/CreateReceptacleForRateTypeToDestination";
        $weight = 0;
        $piecesCount = 0;
        if ($container->services_subclass_code == ShippingService::GSS_PMI) {
            $rateType = "IPA";
            $foreignOECode = "CWB";
        } elseif ($container->services_subclass_code == ShippingService::GSS_EPMEI) {
            $rateType = 'EPMEI';
            $foreignOECode = "SAO";
        } elseif ($container->services_subclass_code == ShippingService::GSS_EPMI) {
            $rateType = 'EPMI';
            $foreignOECode = "RIO";
        } elseif ($container->services_subclass_code == ShippingService::GSS_FCM) {
            $rateType = 'EFCM';
            $foreignOECode = "CWB";
        }elseif($container->services_subclass_code == ShippingService::GSS_EMS || $container->services_subclass_code == ShippingService::GSS_CEP) {
            $rateType = 'EMS';
            $foreignOECode = "CWB";
        }
        // if($containers[0]->awb) {
        //     foreach($containers as $package) {
        $weight = UnitsConverter::kgToPound($container->total_weight);
        $piecesCount = $container->total_orders;
        // }

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

            return $this->addPackagesToReceptacle($data->receptacleID, $container);
        } else {
            return $this->responseUnprocessable($data->message);
        }
        // }
        // else {
        //     return $this->responseUnprocessable("Airway Bill Number is Required for Processing.");
        // }
    }

    public function addPackagesToReceptacle($id, $container)
    {
        $codes = [];
        // foreach ($containers as $key => $container) {
        foreach ($container->orders as $key => $item) {
            $codesToPush = [
                $item->corrios_tracking_code,
            ];
            array_push($codes, $codesToPush);
        }
        // }
        $parcels = implode(",", array_merge(...$codes));
        $url = $this->baseUrl . '/Package/AddPackagesToReceptacle';
        $body = [
            "uspsPackageID" =>  $parcels,
            "receptacleID" => $id,
        ];
        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        $data = json_decode($response);
        if ($response->successful() && $data->success == true) {
            return $this->moveReceptacleToOpenDispatch($id, $container);
        } else {
            return $this->responseUnprocessable($data->message);
        }
    }

    public function moveReceptacleToOpenDispatch($id, $container)
    {
        $url = $this->baseUrl . "/Receptacle/MoveReceptacleToOpenDispatch/$id";
        $response = Http::withHeaders($this->getHeaders())->get($url);
        $data = json_decode($response);
        if ($response->successful() && $data->success == true) {
            return $this->closeDispatch($id, $container);
        } else {
            return $this->responseUnprocessable($data->message);
        }
    }

    public function closeDispatch($id, $container)
    {
        $url = $this->baseUrl . '/Dispatch/CloseDispatch';
        $body = [
            "departureDateTime" => Carbon::now(),
            "arrivalDateTime" => Carbon::now()->addDays(1),
        ];
        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        $data = json_decode($response);
        if ($response->successful() && $data->success == true) {
            return $this->getReceptacleLabel($id, $container, $data->dispatchID);
        } else {
            return $this->responseUnprocessable($data->message);
        }
    }

    public function getReceptacleLabel($id, $container, $dispatchID)
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
            $container->update([
                'unit_response_list' => json_encode(['cn35' => $data, 'manifest' => $reportData, 'dispatchID' => $dispatchID]),
                'unit_code' => $id,
                'response' => 1
            ]);
            return $this->responseSuccessful($data, 'Container registration is successfull. You can download CN35 label');
        } else {
            return $this->responseUnprocessable($data->message);
        }
    }

    public function generateDispatchReport($report, $dispatchID)
    {

        $permitNumber = '';
        if (strpos($report, '_') !== false) {
            $reportID = substr($report, 0, strpos($report, '_'));
            $permitNumber = '4680';
        } else {
            $reportID = $report;
        }

        $url = $this->baseUrl . '/Dispatch/GenerateDispatchReport';
        $body = [
            "dispatchID" => $dispatchID,
            "reportID" => $reportID,
            "permitNumber" => $permitNumber,
            "testMode" => true
        ];

        $response = Http::withHeaders($this->getHeaders())->post($url, $body);
        return $response;
    }

    
    public function getServiceRates($request) {

        $service = $request->service;
        $order = Order::find($request->order_id);

        if($service == ShippingService::GSS_CEP) {

            $shippingService = ShippingService::where('service_sub_class', $service)->first();
            $zoneId = ZoneCountry::where('shipping_service_id', $shippingService->id)
            ->where('country_id', $order->recipient->country_id)
            ->value('group_id');

            $rate = getZoneRate($order, $shippingService, $zoneId);

            if ($rate > 0) {
                return $this->responseSuccessful($rate, 'Rate Calculation Successful');
                
            } else {
                return $this->responseUnprocessable("No Rates Found on Server");
            }

        } else {
            $rateType = '';
            if($order->is_paid){ 
                return $this->responseSuccessful($order->gross_total, 'Rate Calculation Successful');
            }
            if($service == ShippingService::GSS_PMI) {
                $rateType = 'PMI';
            } elseif($service == ShippingService::GSS_EPMEI) {
                $rateType = 'EPMEI';
            } elseif($service == ShippingService::GSS_EPMI) {
                $rateType = 'EPMI';
            } elseif($service == ShippingService::GSS_FCM) {
                $rateType = 'FCM';
            } elseif($service == ShippingService::GSS_EMS) {
                $rateType = 'EMS';
            } elseif($service == ShippingService::GSS_CEP) {
                $rateType = 'CEP';
            }

            $url = $this->baseUrl . '/Utility/CalculatePostage';
            $body = [
                "countryCode" => "BR",
                "postalCode" => $order->recipient->zipcode,
                "rateType" => $rateType,
                "serviceType" => "LBL",
                "packageWeight" => $order->weight,
                "unitOfWeight" => $order->measurement_unit == "lbs/in" ? 'LB' : 'KG',
                "packageLength" => $order->length,
                "packageWidth" => $order->width,
                "packageHeight" => $order->height,
                "unitOfMeasurement" => $order->measurement_unit == "lbs/in" ? 'IN' : 'CM',
                "rateAdjustmentCode" => "NORMAL RATE",
                "nonRectangular" => "0",
                "extraServiceCode" => "",
                "entryFacilityZip" => "",
                "customerReferenceID" => ""
            ];
            $response = Http::withHeaders($this->getHeaders())->post($url, $body);
            $data= json_decode($response);
            if ($response->successful() && $data->success == true) {
                

                $serviceId = ShippingService::where('service_sub_class', $service)->value('id');
                $this->gssProfit = ZoneCountry::where('shipping_service_id', $serviceId)
                                    ->where('country_id', $order->recipient->country_id)
                                    ->value('profit_percentage');
                    if($this->gssProfit) {                
                    $userDiscount =  setting('gss_profit', null, $order->user_id);
                    $userDiscount = ($userDiscount >= 0 && $userDiscount <= 100)?$userDiscount:0;
                    $totalProfit =   $this->gssProfit - ( $this->gssProfit / 100 * $userDiscount );
                    $profit = $data->calculatedPostage / 100 * ($totalProfit);
                    $price = round($data->calculatedPostage + $profit, 2);
                    \Log::info([
                        'service sub class'=> $service,
                        'user id'=> $order->user_id,
                        'user discount'=> $userDiscount,
                        'gss profit percentage '=> $this->gssProfit,
                        'totalProfit =  profit minus discount'=> $totalProfit,
                        'calculatedPostage' => $data->calculatedPostage,
                        'calculatedPostage plus totalProfit'=> $price,
                    ]);
                    return $this->responseSuccessful($price, 'Rate Calculation Successful');
                } else {
                    \Log::info([
                        'service sub class'=> $service, 
                        'recipinet country'=>$order->recipient->country_id,
                        'message'=> 'zone rate not uploaded for recipient country'
                    ]);
                    return $this->responseUnprocessable("Server Error! Rates Not Found");
                }
            } else {
                return $this->responseUnprocessable($data->message);
            }
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

    private function makePDFLabel($response)
    {
        $pdf = PDFMerger::init();
        $label = "app/labels/{$response->trackingNumber}";
        foreach ($response->labels as $index => $labelBase64) {
            $labelContent = base64_decode($labelBase64);
            $pagePath = storage_path("{$label}_{$index}.pdf");
            file_put_contents($pagePath, $labelContent);
            $pdf->addPDF($pagePath);
        }
        $pdf->merge();

        // Remove individual pages
        foreach ($response->labels as $index => $labelBase64) {
            $pagePath = storage_path("{$label}_{$index}.pdf");
            unlink($pagePath);
        }
        $mergedPdf = $pdf->output();
        return base64_encode($mergedPdf);
    }

    public function getCostRates($order, $service) {
        
        if($service->service_sub_class == ShippingService::GSS_CEP) {

            $zoneId = ZoneCountry::where('shipping_service_id', $service->id)
            ->where('country_id', $order->recipient->country_id)
            ->value('group_id');

            $rate = getZoneRate($order, $service, $zoneId);

            if ($rate > 0) {
                return $this->responseSuccessful($rate, 'Rate Calculation Successful');
                
            } else {
                return $this->responseUnprocessable("No Rates Found on Server");
            }

        } else {
            $rateType = '';
            if($service->service_sub_class == ShippingService::GSS_PMI) {
                $rateType = 'PMI';
            } elseif($service->service_sub_class == ShippingService::GSS_EPMEI) {
                $rateType = 'EPMEI';
            } elseif($service->service_sub_class == ShippingService::GSS_EPMI) {
                $rateType = 'EPMI';
            } elseif($service->service_sub_class == ShippingService::GSS_FCM) {
                $rateType = 'FCM';
            } elseif($service->service_sub_class == ShippingService::GSS_EMS) {
                $rateType = 'EMS';
            }
    
            $url = $this->baseUrl . '/Utility/CalculatePostage';
            $body = [
                "countryCode" => "BR",
                "postalCode" => $order->recipient->zipcode,
                "rateType" => $rateType,
                "serviceType" => "LBL",
                "packageWeight" => $order->weight,
                "unitOfWeight" => $order->measurement_unit == "lbs/in" ? 'LB' : 'KG',
                "packageLength" => $order->length,
                "packageWidth" => $order->width,
                "packageHeight" => $order->height,
                "unitOfMeasurement" => $order->measurement_unit == "lbs/in" ? 'IN' : 'CM',
                "rateAdjustmentCode" => "NORMAL RATE",
                "nonRectangular" => "0",
                "extraServiceCode" => "",
                "entryFacilityZip" => "",
                "customerReferenceID" => ""
            ];
            $response = Http::withHeaders($this->getHeaders())->post($url, $body);
            $data= json_decode($response);
            if ($response->successful() && $data->success == true) {
                if($data->calculatedPostage > 0) {
                    return $this->responseSuccessful($data->calculatedPostage, 'Rate Calculation Successful');
                } 
                
            } else {
                return $this->responseUnprocessable($data->message);
            }
        }
    }
}
