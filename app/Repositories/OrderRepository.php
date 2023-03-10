<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\User;
use App\Models\Country;
use App\Facades\USPSFacade;
use Illuminate\Http\Request;
use App\Models\ShippingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\UPS\UPSShippingService;
use App\Services\USPS\USPSShippingService;
use App\Services\FedEx\FedExShippingService;
use App\Services\GePS\GePSShippingService;
use App\Services\Calculators\WeightCalculator;
use App\Services\Colombia\ColombiaPostalCodes;
use App\Services\Order\UpdateOrderInvoice;

class OrderRepository
{
    protected $error;
    protected $chargeID;
    public $shippingServiceError;

    public function get(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='asc', $trashed = false)
    {
        $query = Order::query();
        if ($trashed) {
           $query = $query->onlyTrashed();
        }

        $query = $query->where('status','>=',Order::STATUS_ORDER)
                        ->has('user')
                        ->with([
                            'paymentInvoices',
                            'user',
                            'subOrders',
                            'parentOrder'
                        ]);
        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }

        if($request->userType == 'domestic')
        {
            $query->where('sender_country_id', Country::US);
        }

        if ($request->userType == 'pickups') {
            $query->where('api_pickup_response' , '!=', null);
        }

        if($request->userType){
            $query->whereHas('user', function ($queryUser) use($request) {
                $queryUser->whereHas('role', function ($queryRole) use($request) {
                    return $queryRole->where('name', $request->userType);
                });
            });
        }

        if($request->order_date){
            $query->where('order_date', 'LIKE', "%{$request->order_date}%");
        }
        if($request->name){
            $query->whereHas('user', function ($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->name}%");
            });
        }
        if($request->pobox_number){
            $query->whereHas('user', function ($query) use($request) {
                return $query->where('pobox_number', 'LIKE', "%{$request->pobox_number}%");
            });
        }
        if($request->warehouse_number){
            $query->where('warehouse_number', 'LIKE', "%{$request->warehouse_number}%");
        }
        if($request->merchant){
            $query->where('merchant', 'LIKE', "%{$request->merchant}%");
        }
        if($request->carrier){
            if($request->carrier == 'Brazil'){
                $service = [
                    ShippingService::Packet_Standard,
                    ShippingService::Packet_Express,
                    ShippingService::Packet_Mini
                ];
            }
            if($request->carrier == 'USPS'){
                $service = [
                    ShippingService::USPS_PRIORITY,
                    ShippingService::USPS_FIRSTCLASS,
                    ShippingService::USPS_PRIORITY_INTERNATIONAL,
                    ShippingService::USPS_FIRSTCLASS_INTERNATIONAL,
                    ShippingService::USPS_GROUND
                ];
            }
            if($request->carrier == 'UPS'){
                $service = [
                    ShippingService::UPS_GROUND,
                ];
            }
            if($request->carrier == 'FEDEX'){
                $service = [
                    ShippingService::FEDEX_GROUND
                ];
            }
            if($request->carrier == 'Chile'){
                $service = [
                    ShippingService::SRP,
                    ShippingService::SRM,
                    ShippingService::Courier_Express
                ];
            }
            if($request->carrier == 'Global eParcel'){
                $service = [
                    ShippingService::GePS,
                    ShippingService::GePS_EFormat,
                    ShippingService::Parcel_Post,
                ];
            }
            if($request->carrier == 'Prime5'){
                $service = [
                    ShippingService::Prime5, 
                ];
            }
            if($request->carrier == 'Post Plus'){
                $service = [
                    ShippingService::Post_Plus_Registered,
                    ShippingService::Post_Plus_EMS,
                ];
            }
            if($request->carrier == 'AJ_Standard_CN'){
                $service = [
                    ShippingService::AJ_Standard_CN, 
                ];
            }
            $query->whereHas('shippingService', function ($query) use($service) {
                return $query->whereIn('service_sub_class', $service);
            });
        }
        if($request->gross_total){
            $query->where('gross_total', 'LIKE', "%{$request->gross_total}%");
        }
        if($request->tracking_id){
            $query->where('tracking_id', 'LIKE', "%{$request->tracking_id}%");
        }
        if($request->customer_reference){
            $query->where('customer_reference', 'LIKE', "%{$request->customer_reference}%");
        }
        if($request->corrios_tracking_code){
            $query->where('corrios_tracking_code', 'LIKE', "%{$request->corrios_tracking_code}%");
        }
        if($request->status){
            $query->where('status',$request->status);
        }
        if($request->orderType){
            if ($request->orderType === 'consolidated') {
                $query->where('is_consolidated',true);
            }

            if ($request->orderType === 'non-consolidated') {
                $query->where('is_consolidated',false);
            }
        }
        if($request->paymentStatus){
            if ($request->paymentStatus === 'paid') {
                $query->where('is_paid',true);
            }

            if ($request->paymentStatus === 'unpaid') {
                $query->where('is_paid',false);
            }
        }

        if($request->search){
            $query->where('tracking_id', 'LIKE', "%{$request->search}%")
            ->orWhere('status',$request->status)
            ->orWhere('corrios_tracking_code', 'LIKE', "%{$request->search}%")
            ->orWhere('customer_reference', 'LIKE', "%{$request->search}%")
            ->orWhere('tracking_id', 'LIKE', "%{$request->search}%")
            ->orWhere('gross_total', 'LIKE', "%{$request->search}%")
            ->orWhere('order_date', 'LIKE', "%{$request->search}%")
            ->orWhereHas('user', function ($queryUser) use($request) {
                $queryUser->whereHas('role', function ($queryRole) use($request) {
                    return $queryRole->where('name', $request->search);
                });
            })
            ->orWhere('warehouse_number', 'LIKE', "%{$request->search}%")
            ->orWhereHas('user', function ($query) use($request) {
                return $query->where('pobox_number', 'LIKE', "%{$request->search}%");
            })
            ->orWhereHas('user', function ($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->search}%");
            })
            ->orWhereHas('user', function ($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->search}%");
            })
            ->orWhere('order_date', 'LIKE', "%{$request->search}%");

        }
        $query->orderBy($orderBy,$orderType);

        return $paginate ? $query->paginate($pageSize) : $query->get();
    }

    public function getOrderByIds(array $ids)
    {
        $query = Order::query();

        if ( Auth::user()->isUser() ){
            $query->where('user_id',Auth::id());
        }

        return $query->whereIn('id',$ids)->get();
    }

    public function updateSenderAddress(Request $request, Order $order)
    {
        $order->update([
            'sender_first_name' => $request->first_name,
            'sender_last_name' => $request->last_name,
            'sender_email' => $request->email,
            'sender_phone' => $request->phone,
            'sender_taxId' => $request->taxt_id,
            'sender_address' => $request->sender_address,
            'sender_city' => $request->sender_city,
            'sender_country_id' => $request->sender_country_id,
            'sender_state_id' => $request->sender_state_id,
            'sender_zipcode' => $request->sender_zipcode,
        ]);

        return $order;
    }

    public function updateRecipientAddress(Request $request, Order $order)
    {
        $order->update([
            'recipient_address_id' => $request->address_id
        ]);

        if ( $request->has('save_address') && !$request->address_id){
            (new AddressRepository)->store($request);
        }

        if ( $request->has('save_address') && $request->address_id ){
            session()->flash('alert-danger',__('address.duplicate_error'));
        }

        $request->merge([
            'phone' => "+".cleanString($request->phone)
        ]);
        
        if ( $order->recipient ){
            if($request->service == 'postal_service' && $request->country_id == Country::COLOMBIA) {
                $city = $request->cocity;
            }
            $city = $request->city;
            $order->recipient()->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'city' => $city,
                'commune_id' => ($request->service == 'courier_express') ? $request->commune_id : null,
                'street_no' => ($request->country_id == Country::COLOMBIA)? $request->codept : $request->street_no,
                'address' => $request->address,
                'address2' => $request->address2,
                'account_type' => $request->account_type,
                'tax_id' => cleanString($request->tax_id),
                'zipcode' => cleanString($request->zipcode),
                'state_id' => $request->state_id,
                'country_id' => $request->country_id,
                'region' => $request->region,
            ]);

            return $order->recipient;

        }

        $order->recipient()->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => ($request->service == 'postal_service') ? $request->city : null,
            'commune_id' => ($request->service == 'courier_express') ? $request->commune_id : null,
            'street_no' => $request->street_no,
            'address' => $request->address,
            'address2' => $request->address2,
            'account_type' => $request->account_type,
            'tax_id' => $request->tax_id,
            'zipcode' => $request->zipcode,
            'state_id' => $request->state_id,
            'country_id' => $request->country_id,
            'region' => $request->region,
        ]);

        $order->refresh();

        return $order->recipient;
    }

    public function updateHandelingServices(Request $request, Order $order)
    {
        $order->syncServices($request->get('services',[]));

        $order->doCalculations();
        return true;
    }

    public function GePSService($shippingServiceId)
    {
        $shippingService =  ShippingService::find($shippingServiceId);

        if ($shippingService->isGePSService()) {
            return true;
        }

        return false;
    }

    public function GePSeFormatService($shippingServiceId)
    {
        $shippingService =  ShippingService::find($shippingServiceId);

        if ($shippingService->isGePSeFormatService()) {
            return true;
        }

        return false;
    }

    public function serviceRequireFreight($shippingServiceId)
    {
        $shippingService =  ShippingService::find($shippingServiceId);

        if ($shippingService->isDomesticService()) {
            return true;
        }

        return false;
    }

    public function updateShippingAndItems(Request $request, Order $order)
    {
        DB::beginTransaction();
        $oldOrder = clone $order;

        try {

            if ($order->products->isEmpty()) {

                $order->items()->delete();

                foreach ($request->get('items',[]) as $item) {

                    $order->items()->create([
                        'sh_code' => optional($item)['sh_code'],
                        'description' => optional($item)['description'],
                        'quantity' => optional($item)['quantity'],
                        'value' => optional($item)['value'],
                        'contains_battery' => optional($item)['dangrous_item'] == 'contains_battery' ? true: false,
                        'contains_perfume' => optional($item)['dangrous_item'] == 'contains_perfume' ? true: false,
                        'contains_flammable_liquid' => optional($item)['dangrous_item'] == 'contains_flammable_liquid' ? true: false,
                    ]);
                }
            }

            $shippingService = ShippingService::find($request->shipping_service_id);

            if ($shippingService->service_sub_class == ShippingService::USPS_PRIORITY_INTERNATIONAL ||  $shippingService->service_sub_class == ShippingService::USPS_FIRSTCLASS_INTERNATIONAL) {
                if(!$this->getUSPSInternationServiceRates($request, $order, $shippingService->service_sub_class))
                {
                    DB::rollback();
                    session()->flash('alert-danger','orders.Error While placing Order '.$this->error);
                    return false;
                }
            }

            $order->update([
                'customer_reference' => $request->customer_reference,
                'shipping_service_id' => $shippingService->id,
                'shipping_service_name' => $shippingService->name,
                'tax_modality' => $request->tax_modality,
                'is_invoice_created' => true,
                'user_declared_freight' => $request->user_declared_freight,
                'comission' => 0,
                'insurance_value' => 0,
                'status' => $order->status < Order::STATUS_ORDER ?  Order::STATUS_ORDER : $order->status
            ]);

            $order->doCalculations();

            // if ($order->isPaid() && $order->getPaymentInvoice()) 
            // {
            //     $orderInvoice = $order->getPaymentInvoice();
               
            //     $orderInvoice->update([
            //         'total_amount' => $orderInvoice->orders()->sum('gross_total'),
            //     ]);

            //     if ($orderInvoice->total_amount > $orderInvoice->paid_amount) {
                    
            //         $orderInvoice->update([
            //             'is_paid' => 0,
            //         ]);
                    
            //         $order->update([
            //             'status' => Order::STATUS_PAYMENT_PENDING,
            //             'is_paid' => 0,
            //         ]);
            //     }
            // }

            if((new UpdateOrderInvoice())->update($order,$oldOrder)){ 
                    DB::commit();
                    session()->flash('alert-success','orders.Sender Updated');
                    return true;
            }
            else{
            DB::rollback();
            $this->error = 'Unable to update the order.';
             session()->flash('alert-danger','orders.Error While placing Order'." ".$this->error);
            return false;
            }
        } catch (\Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
             session()->flash('alert-danger','orders.Error While placing Order'." ".$this->error);
            return false;
        }
    }

    public function getError()
    {
        return $this->error;
    }
    
    public function getOdersForExport($request, $user)
    {
        $orders = Order::where('status','>=',Order::STATUS_ORDER)
        ->has('user');

        if ($user->isUser()) {
            $orders->where('user_id', $user->id);
        }
        if ($request->type == 'domestic') {
            $orders->whereHas('shippingService', function($query) {
                return $query->whereIn('service_sub_class', [ShippingService::USPS_PRIORITY,ShippingService::USPS_FIRSTCLASS,ShippingService::UPS_GROUND, ShippingService::FEDEX_GROUND, ShippingService::USPS_GROUND]);
            })->orWhereNotNull('us_api_tracking_code');
        }

        $startDate  = $request->start_date.' 00:00:00';
        $endDate    = $request->end_date.' 23:59:59';
        if ( $request->start_date ){
            $orders->where('order_date' , '>=',$startDate);
        }
        if ( $request->end_date ){
            $orders->where('order_date' , '<=',$endDate);
        }

        return $orders->orderBy('id')->get();
    }

    public function setVolumetricDiscount($order)
    {
        $totalDiscountPercentage = 0;
        $volumetricDiscount = setting('volumetric_discount', null, $order->user->id);
        $discountPercentage = setting('discount_percentage', null, $order->user->id);

        if (!$volumetricDiscount || !$discountPercentage || $discountPercentage < 0 || $discountPercentage == 0) {
            return false;
        }
        if ( $order->measurement_unit == 'kg/cm' ){
            $volumetricWeight = WeightCalculator::getVolumnWeight($order->length,$order->width,$order->height,'cm');
        }else {
            $volumetricWeight = WeightCalculator::getVolumnWeight($order->length,$order->width,$order->height,'in');
}
        $volumeWeight = round($volumetricWeight > $order->weight ? $volumetricWeight : $order->weight,2);
        $totalDiscountPercentage = ($discountPercentage) ? $discountPercentage/100 : 0;
        
        if ($volumeWeight > $order->weight) {

            $consideredWeight = $volumeWeight - $order->weight;
            $volumeWeight = round($consideredWeight - ($consideredWeight * $totalDiscountPercentage), 2);
            $totalDiscountedWeight = $consideredWeight - $volumeWeight;
            $order->update([
                'weight_discount' => $totalDiscountedWeight,
            ]);
        }

        return true;
    }

    public function getShippingServices($order)
    {
        $shippingServicesWithoutRates = ShippingService::query()->active()->get();
        $shippingServices = collect() ;

        if(optional($order->recipient)->country_id == Order::US)
        {
            $uspsShippingService = new USPSShippingService($order);
            $upsShippingService = new UPSShippingService($order);
            $fedExShippingService = new FedExShippingService($order);

            foreach ($shippingServicesWithoutRates as $shippingService)
            {
                if ($uspsShippingService->isAvailableFor($shippingService)) {
                    $shippingServices->push($shippingService);
                }

                if ($upsShippingService->isAvailableFor($shippingService)) {
                    $shippingServices->push($shippingService);
                }

                if ($fedExShippingService->isAvailableFor($shippingService)) {
                    $shippingServices->push($shippingService);
                }
            }
        }else
        {
            foreach (ShippingService::query()->has('rates')->active()->get() as $shippingService)
            {
                if ($shippingService->isAvailableFor($order)) {
                    $shippingServices->push($shippingService);
                }elseif($shippingService->getCalculator($order)->getErrors() != null && $shippingServices->isEmpty()){
                    $this->shippingServiceError = 'Shipping Service not Available Error: {'.$shippingService->getCalculator($order)->getErrors().'}';
                }
            }
            // USPS Intenrational Services
            if (optional($order->recipient)->country_id != Order::US && setting('usps', null, User::ROLE_ADMIN))
            {

                $uspsShippingService = new USPSShippingService($order);

                foreach (ShippingService::query()->active()->get() as $shippingService)
                {
                    if ($uspsShippingService->isAvailableForInternational($shippingService)) {
                        $shippingServices->push($shippingService);
                    }
                }
            }
            // GePS Service
            if (optional($order->recipient)->country_id != Order::US && setting('geps_service', null, User::ROLE_ADMIN) && setting('geps_service', null, auth()->user()->id))
            {

                $gepsShippingService = new GePSShippingService($order);

                foreach ($gepsShippingService as $shippingService)
                {
                    if ($gepsShippingService->isAvailableForInternational($shippingService)) {
                        $shippingServices->push($shippingService);
                    }
                }
            }

            if ($shippingServices->isEmpty() && $this->shippingServiceError == null) {
                $this->shippingServiceError = ($order->recipient->commune_id != null) ? 'Shipping Service not Available for the Region you have selected' : 'Shipping Service not Available for the Country you have selected';
            }
        }
        if ($shippingServices->isNotEmpty()) {
           $shippingServices = $this->filterShippingServices($shippingServices, $order);
        }
        return $shippingServices;
    }

    public function getShippingServicesError()
    {
        return $this->shippingServiceError;
    }

    private function filterShippingServices($shippingServices, $order)
    {
        if($shippingServices->contains('service_sub_class', ShippingService::USPS_PRIORITY)
            || $shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS)
            || $shippingServices->contains('service_sub_class', ShippingService::USPS_PRIORITY_INTERNATIONAL)
            || $shippingServices->contains('service_sub_class', ShippingService::USPS_FIRSTCLASS_INTERNATIONAL)
            || $shippingServices->contains('service_sub_class', ShippingService::UPS_GROUND)
            || $shippingServices->contains('service_sub_class', ShippingService::GePS)
            || $shippingServices->contains('service_sub_class', ShippingService::GePS_EFormat)
            || $shippingServices->contains('service_sub_class', ShippingService::USPS_GROUND)
            || $shippingServices->contains('service_sub_class', ShippingService::Parcel_Post)
            || $shippingServices->contains('service_sub_class', ShippingService::PostNL))
        {
            if(!setting('usps', null, User::ROLE_ADMIN))
            {
                $this->shippingServiceError = 'USPS is not enabled for this user';
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::USPS_PRIORITY
                        && $shippingService->service_sub_class != ShippingService::USPS_FIRSTCLASS
                        && $shippingService->service_sub_class != ShippingService::USPS_PRIORITY_INTERNATIONAL
                        && $shippingService->service_sub_class != ShippingService::USPS_FIRSTCLASS_INTERNATIONAL
                        && $shippingService->service_sub_class != ShippingService::USPS_GROUND;
                });
            }
            if(!setting('ups', null, User::ROLE_ADMIN))
            {
                $this->shippingServiceError = 'UPS is not enabled for this user';
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::UPS_GROUND;
                });
            }

            if (!setting('fedex', null, User::ROLE_ADMIN)) {
                $this->shippingServiceError = 'FEDEX is not enabled for this user';
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::FEDEX_GROUND;
                });
            }

            if (!setting('geps_service', null, User::ROLE_ADMIN) && !setting('geps_service', null, auth()->user()->id)) {
                $this->shippingServiceError = 'GePS is not enabled for this user';
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::GePS;
                });
            }
            
            if (!setting('postnl_service', null, User::ROLE_ADMIN) && !setting('postnl_service', null, auth()->user()->id)) {
                $this->shippingServiceError = 'PostNL is not enabled for this user';
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::PostNL;
                });
            }

            if($shippingServices->isNotEmpty()){
                $this->shippingServiceError = null;
            }
        }

        if($order->recipient->country_id == Order::BRAZIL)
        {
            // If sinerlog is enabled for the user, then remove the Correios services
            if(setting('sinerlog', null, $order->user->id))
            {
                $shippingServices = $shippingServices->filter(function ($item, $key)  {
                    return $item->service_sub_class != '33162' && $item->service_sub_class != '33170' && $item->service_sub_class != '33197';
                });
            }

            // If sinerlog is not enabled for the user then remove Sinerlog services from shipping service
            if(!setting('sinerlog', null, $order->user->id))
            {
                $shippingServices = $shippingServices->filter(function ($item, $key)  {
                    return $item->service_sub_class != '33163' && $item->service_sub_class != '33171' && $item->service_sub_class != '33198';
                });
            }

            if(setting('anjun_api', null, \App\Models\User::ROLE_ADMIN)){
                    $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                        return $shippingService->service_sub_class != ShippingService::Packet_Standard
                            && $shippingService->service_sub_class != ShippingService::Packet_Express
                            && $shippingService->service_sub_class != ShippingService::Packet_Mini;
                    });
            }

            if(!setting('anjun_api', null, \App\Models\User::ROLE_ADMIN)){
                    $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                        return $shippingService->service_sub_class != ShippingService::AJ_Packet_Standard
                            && $shippingService->service_sub_class != ShippingService::AJ_Packet_Express;
                    });
            }

            if($shippingServices->isEmpty()){
                $this->shippingServiceError = 'Please check your parcel dimensions';
            }
        }

        if($shippingServices->contains('service_sub_class', ShippingService::COLOMBIA_URBANO)
            || $shippingServices->contains('service_sub_class', ShippingService::COLOMBIA_NACIONAL)
            || $shippingServices->contains('service_sub_class', ShippingService::COLOMBIA_TRAYETOS)) {

            $colombiaPostalCodeService = new ColombiaPostalCodes();
            $service = $colombiaPostalCodeService->getServiceByPostalCode($order->recipient->zipcode);

            if($service) {
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) use($service) {
                    return $shippingService->service_sub_class == $service;
                });
            } else {
                $shippingServices = $shippingServices->filter(function ($shippingService, $key) {
                    return $shippingService->service_sub_class != ShippingService::COLOMBIA_URBANO
                        && $shippingService->service_sub_class != ShippingService::COLOMBIA_NACIONAL
                        && $shippingService->service_sub_class != ShippingService::COLOMBIA_TRAYETOS;
                });
            }

        } 

        return $shippingServices;
    }

    private function getUSPSInternationServiceRates($request, $order, $service)
    {
        $response  = USPSFacade::getRecipientRates($order, $service);
        if ($response->success == true) {

            $request->merge([
                'user_declared_freight' => $response->data['total_amount'],
            ]);

            return true;
        }
        $this->error = $response->message;
        return false;
    }

}
