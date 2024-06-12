<?php

namespace App\Http\Controllers\Api\PublicApi;

use App\Models\User;
use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Repositories\USLabelRepository;
use App\Repositories\DomesticLabelRepository;
use App\Repositories\Api\DomesticRateRepository;
use App\Repositories\ConsolidateDomesticLabelRepository;

class DomesticLabelController extends Controller
{
    public $uspsProfit;
    public $upsProfit;
    public $fedExProfit;
    public $serviceRate;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function __invoke(Request $request, ConsolidateDomesticLabelRepository $consolidatedDomesticLabelRepository, USLabelRepository $usLabelRepostory, DomesticLabelRepository $domesticLabelRepository, DomesticRateRepository $domesticRateRepository)
    {
        $validated = $request->validate([
            'warehouse_no' => 'required',
            'weight' => 'required',
            'unit' => 'required',
            'length' => 'required',
            'width' => 'required',
            'height' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'sender_address' => 'required',
            'sender_city' => 'required',
            'sender_zipcode' => 'required',
            'sender_state' => 'required',
            'sender_country' => 'required',
            'recipient.first_name' => 'required',
            'recipient.streetLines' => 'required',
            'recipient.city' => 'required',
            'recipient.postalCode' => 'required',
            'recipient.stateOrProvinceCode' => 'required',
            'recipient.receipeint_phone' => 'required',
            'recipient.country' => 'required',
        ]);

        if (!is_numeric($request->recipient['country'])){
            $country = Country::where('code', $request->recipient['country'])->orwhere('id', $request->recipient['country'])->first();
            $request->merge(['destination_country' => $country->id]);
        }

        if (!is_numeric($request->sender_country)){
            $country = Country::where('code', $request->sender_country)->orwhere('id', $request->sender_country)->first();
            $request->merge(['origin_country' => $country->id]);
        }

        $request->merge(['recipient_address' => $request->recipient['streetLines'], 'recipient_city' => $request->recipient['city'], 'recipient_zipcode' => $request->recipient['postalCode'], 'recipient_state' => $request->recipient['stateOrProvinceCode'], 'consolidated_order' => true]);

        $orders = $consolidatedDomesticLabelRepository->getInternationalOrders($request->warehouse_no);
        if ($orders->isEmpty()) {
            return apiResponse(false,"Selected orders already have domestic label");
        }

        //GET RATES FROM DOMESTIC SERVICES
        $rates = $domesticRateRepository->domesticServicesRates($request);
        $serviceRate = $rates->getData()->data->rates;
        $this->serviceRate = $serviceRate;

        $error = $consolidatedDomesticLabelRepository->getErrors();

        if(!$error && $this->serviceRate ){
            //SET LABLE PRICE AS PER PROFIT SETTING
            $this->setUserProft(request()->service);
            //GET TOTAL WEIGHT OF ORDERS
            $totalWeight = $consolidatedDomesticLabelRepository->getTotalWeight($orders);

            if(request()->unit == 'kg/cm' && request()->weight > $totalWeight['totalWeightInKg'] || request()->unit == 'lbs/in' && request()->weight > $totalWeight['totalWeightInLbs']) {             
                request()->merge(['user' => $orders->first()->user, 'orders' => $orders]);
                $domesticLabelRepository->handle();
                $tempOrder = $orders->first();
                request()->merge(['order' => $tempOrder]);
                $label = $domesticLabelRepository->getDomesticLabel(request()->order);

                return apiResponse(true,"Label Generated Successfully.",[
                    'url' => route('order.us-label.download',$tempOrder),
                    'tracking_code' => $tempOrder->us_api_tracking_code
                ]);
            
            }
            return apiResponse(false,"The weight provided is invalid.",[
                'error' => "Given weight must be greater than the order weight.",
            ]);

        }
        return apiResponse(false, $error);
    }

    private function setUserProft($service)
    {
        //CHECK IF USER HAS PROFIT SETTING
        if (Auth::check()) {
            $this->uspsProfit = setting('usps_profit', null, auth()->user()->id);
            $this->upsProfit = setting('ups_profit', null, auth()->user()->id);
            $this->fedExProfit = setting('fedex_profit', null, auth()->user()->id);
        }
        //APPLY ADMIN SIDE PROFIT SETTING
        if($this->uspsProfit == null || $this->uspsProfit == 0)
        { $this->uspsProfit = setting('usps_profit', null, User::ROLE_ADMIN); }

        if($this->upsProfit == null || $this->upsProfit == 0)
        { $this->upsProfit = setting('ups_profit', null, User::ROLE_ADMIN); }

        if($this->fedExProfit == null || $this->fedExProfit == 0)
        { $this->fedExProfit = setting('fedex_profit', null, User::ROLE_ADMIN); }
        //CALCULATE TOTAL PRICE FOR LABEL
        $price = 0;
        if($service == ShippingService::USPS_PRIORITY || $service == ShippingService::USPS_FIRSTCLASS && setting('usps', null, User::ROLE_ADMIN) && setting('usps', null, auth()->user()->id)) { 
            $profit = optional(optional($this->serviceRate)[0])->rate * ($this->uspsProfit / 100);
            $price = round(optional(optional($this->serviceRate)[0])->rate + $profit, 2);
        }
        if($service == ShippingService::UPS_GROUND && setting('ups', null, User::ROLE_ADMIN) && setting('ups', null, auth()->user()->id)) { 
            $profit = optional(optional($this->serviceRate)[1])->rate * ($this->upsProfit / 100);
            $price = round(optional(optional($this->serviceRate)[1])->rate + $profit, 2);
        }
        if($service == ShippingService::FEDEX_GROUND && setting('fedex', null, User::ROLE_ADMIN) && setting('fedex', null, auth()->user()->id)) { 
            $profit = optional(optional($this->serviceRate)[2])->rate * ($this->fedExProfit / 100);
            $price = round(optional(optional($this->serviceRate)[2])->rate + $profit, 2);
        }
        if($price > 0){
            request()->merge(['total_price' => $price]); 
        }
    }
}
