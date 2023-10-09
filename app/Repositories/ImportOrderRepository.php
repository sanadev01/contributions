<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\ImportOrder;
use Illuminate\Http\Request;
use App\Models\ImportedOrder;
use App\Models\ShippingService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use App\Services\Excel\Import\OrderImportService;
use App\Services\Excel\Import\XmlOrderImportService;
use App\Services\Excel\Import\ShopifyOrderImportService;

class ImportOrderRepository
{
    public function store($request)
    {
        if($request->format == 'homedelivery'){
            $importExcelService = new OrderImportService($request->file('excel_file'),$request);
            $importOrder = $importExcelService->handle();
            return;
        }
       
        if($request->format == 'xml'){
            $importXmlService = new XmlOrderImportService($request);
            $importOrder = $importXmlService->handle();
            
            return;
        }

        $importExcelService = new ShopifyOrderImportService($request->file('excel_file'),$request);
        $importOrder = $importExcelService->handle();
        return;
        
    }

    public function get(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='DESC')
    {

        $query = ImportOrder::query()->has('user');
        
        if ( Auth::user()->isUser() ){
            $query->where('user_id',Auth::id());
        }
        
        if ( $request->date ){
            $query->where(function($query) use($request){
                return $query->where('created_at', 'LIKE', "%{$request->date}%");
            });
        }
        if ( $request->file_name ){
            $query->where(function($query) use($request){
                return $query->where('file_name', 'LIKE', "%{$request->file_name}%");
            });
        }
        if ( $request->total ){
            $query->where(function($query) use($request){
                return $query->where('total_orders', $request->total);
            });
        }
        
        if ( $request->name ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->name}%");
            });
        }
        
        $importOrders = $query
        ->orderBy($orderBy,$orderType);

        return $paginate ? $importOrders->paginate($pageSize) : $importOrders->get();
    }

    public function delete(ImportOrder $importOrder)
    {
        
        $importOrder->delete();
        if($importOrder->importOrders){
            $importOrder->importOrders()->delete();
        }
        return true;
    }

    public function getImportedOrder(Request $request,$paginate = true,$pageSize=50,$orderBy = 'id',$orderType='DESC', $orderId)
    {
        $query = ImportedOrder::query()->has('user');
        
        if ( Auth::user()->isUser() ){
            $query->where('user_id',Auth::id());
        }
        
        if (  $orderId ){
            $query->where(function($query) use($orderId){
                return $query->where('import_id',  $orderId);
            });
        }
        
        if ( $request->date ){
            $query->where(function($query) use($request){
                return $query->where('created_at', 'LIKE', "%{$request->date}%");
            });
        }
        
        if ( $request->client ){
            $query->where(function($query) use($request){
                return $query->where('merchant', 'LIKE', "%{$request->client}%");
            });
        }
        
        if ( $request->carrier ){
            $query->where(function($query) use($request){
                return $query->where('carrier', 'LIKE', "%{$request->carrier}%");
            });
        }
        
        if ( $request->tracking ){
            $query->where(function($query) use($request){
                return $query->where('tracking_id', 'LIKE', "%{$request->tracking}%");
            });
        }
        
        if ( $request->reference ){
            $query->where(function($query) use($request){
                return $query->where('customer_reference', 'LIKE', "%{$request->reference}%");
            });
        }
        
        if ( $request->type == 'error'){
            $query->where(function($query) use($request){
                return $query->where('error', '!=', null);
            });
        }
        if($request->type == 'good'){
            $query->where(function($query) use($request){
                return $query->where('error', null);
            });
        }
       
        if ( $request->name ){
            $query->whereHas('user',function($query) use($request) {
                return $query->where('name', 'LIKE', "%{$request->name}%");
            });
        }
        
        $importedOrders = $query
        ->orderBy($orderBy,$orderType);

        return $paginate ? $importedOrders->paginate($pageSize) : $importedOrders->get();
    }

    public function importedOrderDelete(ImportedOrder $importedOrder)
    {
        $importedOrder->delete();
        return true;
    }


    public function storeOrder(ImportedOrder $importedOrder)
    {
        
        $shippingService = ShippingService::find($importedOrder->shipping_service_id);
        $user = Auth::user();
        $order = Order::create([
            'user_id' => $importedOrder->user_id,
            'shipping_service_id' => $importedOrder->shipping_service_id,
            'shipping_service_name' => $importedOrder->shipping_service_name,
            "merchant" => $importedOrder->merchant,
            "carrier" => $importedOrder->carrier,
            "tracking_id" => $importedOrder->tracking_id,
            "customer_reference" => $importedOrder->customer_reference,

            "weight" => $importedOrder->weight,
            "length" => $importedOrder->length,
            "width" => $importedOrder->width,
            "height" => $importedOrder->height,
            "measurement_unit" => $importedOrder->measurement_unit,
            "is_invoice_created" => $importedOrder->is_invoice_created,
            "is_shipment_added" => $importedOrder->is_shipment_added,
            'status' => $importedOrder->status,
            'order_date' => $importedOrder->order_date, 

            "sender_first_name" => $importedOrder->sender_first_name ? $importedOrder->sender_first_name : optional($user)->name,
            "sender_last_name" => $importedOrder->sender_last_name ? $importedOrder->sender_last_name : optional($user)->last_name,
            "sender_email" => $importedOrder->sender_email ? $importedOrder->sender_email : optional($user)->email ,
            "sender_phone" => $importedOrder->sender_phone ? $importedOrder->sender_phone : optional($user)->phone,
            "user_declared_freight" => $importedOrder->user_declared_freight?$importedOrder->user_declared_freight:"0.01",

        ]);
        $orderRepository = new OrderRepository();
        $orderRepository->setVolumetricDiscount($order);
        $order->recipient()->create([
            "first_name" =>optional($importedOrder->recipient)['first_name'],
            "last_name" => optional($importedOrder->recipient)['last_name'],
            "email" => optional($importedOrder->recipient)['email'],
            "phone" => optional($importedOrder->recipient)['phone'],
            "address" => optional($importedOrder->recipient)['address'],
            "address2" => optional($importedOrder->recipient)['address2'],
            "street_no" => optional($importedOrder->recipient)['street_no'],
            "zipcode" => optional($importedOrder->recipient)['zipcode'],
            "city" => optional($importedOrder->recipient)['city'],
            "account_type" => optional($importedOrder->recipient)['account_type'],
            "state_id" => optional($importedOrder->recipient)['state_id'],
            "country_id" => optional($importedOrder->recipient)['country_id'],
            "tax_id" => optional($importedOrder->recipient)['tax_id'],
        ]);

        $shippingPrice = $shippingService->getRateFor($order);
        
       

        foreach($importedOrder->items as $item){
            $this->addItem($order,$item);
        }

        $order->doCalculations();
        
        $order->update([
            'warehouse_number' => $order->getTempWhrNumber(),
            'user_declared_freight' => $importedOrder->user_declared_freight?$importedOrder->user_declared_freight:"0.01",
        ]);
        
    }

    public function addItem($order, $item)
    {
        $order->items()->create([
            "quantity" => optional($item)['quantity'],
            "value" => optional($item)['value'],
            "description" => optional($item)['description'],
            "sh_code" => optional($item)['sh_code'],
            "contains_battery" => optional($item)['contains_battery'],
            "contains_perfume" => optional($item)['contains_perfume'],
        ]);
    }
    public function storeOrderAll($id)
    {
        $query = ImportedOrder::query()->has('user');
        
        if ( Auth::user()->isUser() ){
            $query->where('user_id',Auth::id());
        }
        $query->where(function($query) use($id){
            return $query->where('error', null);
        });
        if ($id){
            $query->where(function($query) use($id){
                return $query->where('import_id',  $id);
            });
        }

        $orders = $query->get();
       
        foreach($orders as $order){

            if($order){
                $this->storeOrder($order);
                $this->importedOrderDelete($order);
            }
        }
        return;
        
    }

}