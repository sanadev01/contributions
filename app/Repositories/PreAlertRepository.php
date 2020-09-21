<?php

namespace App\Repositories;

use App\Models\Document;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class PreAlertRepository
{
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
                'warehouse_number' => "TEMPWHR-{$order->id}"
            ]);
        }

        if ( $request->hasFile('invoiceFile') ){
            $invoice = Document::saveDocument($image);
                $order->purchaseInvoice()->create([
                    'name' => $invoice->getClientOriginalName(),
                    'size' => $invoice->getSize(),
                    'type' => $invoice->getMimeType(),
                    'path' => $invoice->filename
                ]);
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

        if ( Auth::user()->can('addShipmentDetails',Order::class) ){
            $request->merge([
                'measurement_unit' => $request->unit,
                'is_shipment_added' => true,
                'status' => Order::STATUS_PREALERT_READY
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

        return $order;
    }

    public function delete(Order $order,$soft=true)
    {
        if ( $soft ){
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
}
