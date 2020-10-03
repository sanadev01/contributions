<?php

namespace App\Repositories;

use App\Mail\User\ConsolidationRequest;
use App\Mail\User\OrderCombined;
use App\Mail\User\ShipmentReady;
use App\Models\Document;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Mail;

class PreAlertRepository
{

    protected $error;

    public function getReadyParcels(Request $request, $includeConsolidated=false)
    {
        $query = (new Order())->query()->parcelReady();

        if ( !$includeConsolidated ){
            $query->doesntHave('parentOrder');
        }

        $query->where('user_id',$request->user_id);

        return $query->get();
    }

    public function store(Request $request)
    {
        $data = [];

        $request->merge([
            'status' => Order::STATUS_PREALERT_TRANSIT
        ]);

        $data = [ 'merchant', 'carrier', 'tracking_id', 'order_date','customer_reference', 'user_id','status'];

        if ( Auth::user()->can('addWarehouseNumber',Order::class) ){
            $request->merge([
                'warehouse_number' => $request->whr_number
            ]);
            $data[] = 'warehouse_number';
        }

        if ( Auth::user()->can('addShipmentDetails',Order::class) ){
            $request->merge([
                'measurement_unit' => $request->unit,
                'is_shipment_added' => true,
                'status' => Order::STATUS_PREALERT_READY
            ]);

            $data[] = 'weight';
            $data[] = 'is_shipment_added';
            $data[] = 'measurement_unit';
            $data[] = 'length';
            $data[] = 'width';
            $data[] = 'height';
        }

        if ( !Auth::user()->isAdmin() ){
            $request->merge([
                'user_id' => Auth::id()
            ]);
        }

        $order = Order::create(
            $request->only($data)
        );

        if ( !Auth::user()->isAdmin() && Auth::user()->can('addShipmentDetails',Order::class) ){
            $order->update([
                'warehouse_number' => $order->getTempWhrNumber()
            ]);
        }

        if ( $request->hasFile('invoiceFile') ){
            $order->attachInvoice( $request->file('invoiceFile') );
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $document = Document::saveDocument($image);
                $order->images()->create([
                    'name' => $document->getClientOriginalName(),
                    'size' => $document->getSize(),
                    'type' => $document->getMimeType(),
                    'path' => $document->filename
                ]);
            }
        }

        try {
            \Mail::send(new ShipmentReady($order));
        } catch (\Exception $ex) {
            \Log::info('Shipment ready email send error: '.$ex->getMessage());
        }

        return $order;
    }

    public function update(Request $request, Order $order)
    {
        $data = [];

        $data = [ 'merchant', 'carrier', 'tracking_id','customer_reference', 'order_date'];

        if ( Auth::user()->can('addWarehouseNumber',Order::class) ){
            $request->merge([
                'warehouse_number' => $request->whr_number
            ]);
            $data[] = 'warehouse_number';
        }

        $status = $order->status;

        if ( $order->status < Order::STATUS_ORDER ){
            $status = $order->isConsolidated() ? Order::STATUS_CONSOLIDATED : Order::STATUS_PREALERT_READY;
        }

        if ( Auth::user()->can('addShipmentDetails',Order::class) ){
            $request->merge([
                'measurement_unit' => $request->unit,
                'is_shipment_added' => true,
                'status' => $status
            ]);

            $data[] = 'status';
            $data[] = 'is_shipment_added';
            $data[] = 'weight';
            $data[] = 'measurement_unit';
            $data[] = 'length';
            $data[] = 'width';
            $data[] = 'height';

        }

        $order->update(
            $request->only($data)
        );

        if ( $request->hasFile('invoiceFile') ){
            $order->attachInvoice( $request->file('invoiceFile') );
        }

        if ($request->hasFile('images')) {
            foreach ($order->images as $oldImage) {
                $oldImage->delete();
            }

            foreach ($request->file('images') as $image) {
                $document = Document::saveDocument($image);
                $order->images()->create([
                    'name' => $document->getClientOriginalName(),
                    'size' => $document->getSize(),
                    'type' => $document->getMimeType(),
                    'path' => $document->filename
                ]);
            }
        }

        if( $order->isConsolidated() ){
            try {
                \Mail::send(new OrderCombined($order));
            } catch (\Exception $ex) {
                \Log::info('Consolidation email send error: '.$ex->getMessage());
            }
        }

        return $order;
    }

    public function delete(Order $order,$soft=true)
    {
        if ( $soft ){
            
            if ( $order->isConsolidated() ){
                $order->subOrders()->sync([]);
            }

            $order->delete();
            return true;
        }

        DB::beginTransaction();

        try {
            $order->items()->delete();
            $order->subOrders()->sync([]);
            optional($order->purchaseInvoice)->delete();
            $order->recipient()->delete();
            foreach ($order->images as $image) {
                $image->delete();
            }
            $order->delete();
            DB::commit();

            return true;
        } catch (\Exception $ex) {
            DB::rollback();

            dd($ex);
            return false;
        }
    }

    public function createConsolidationRequest(Request $request)
    {
        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => Auth::user()->isAdmin() ? $request->user_id : Auth::id(),
                'is_consolidated' => true,
                'merchant' => 'HD-Consolidation-Service',
                'carrier' => 'HD-Consolidation-Service',
                'tracking_id' => 'HD-Consolidation-Service',
                'status' => Order::STATUS_CONSOLIDATOIN_REQUEST
            ]);

            $order->subOrders()->sync($request->parcels);

            $order->update([
                'warehouse_number' => "HD-{$order->id}-C"
            ]);
            
            DB::commit();

            try {
                \Mail::send(new ConsolidationRequest($order));
            } catch (\Exception $ex) {
                \Log::info('Consolidation request email send error: '.$ex->getMessage());
            }

            return $order;

        } catch (\Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function updateConsolidationRequest(Request $request, Order $parcel)
    {
        try {
            DB::beginTransaction();

            $parcel->subOrders()->sync($request->parcels);

            DB::commit();

            return $parcel;

        } catch (\Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    public function getError()
    {
        return $this->error;
    }
}
