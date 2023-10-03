<?php

namespace App\Repositories\Inventory;

use Exception;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\Admin\InventoryOrderPlaced;
use App\Services\Converters\UnitsConverter;

class OrderRepository
{

    public function getOdersForExport($request)
    {
        $orders = Order::has('products');
        if ($request->pick) {
            $orders->where('status','>=',Order::STATUS_INVENTORY_FULFILLED);
        }else{
            $orders->where('status','<=',Order::STATUS_INVENTORY_REJECTED);
        }
        
        if (Auth::user()->isUser()) {
            $orders->where('user_id', Auth::id());
        }
        $startDate  = $request->start_date.' 00:00:00';
        $endDate    = $request->end_date.' 23:59:59';
        
        if ( $request->start_date ){
            $orders->where('order_date','>=',$startDate);
        }
        if ( $request->end_date ){
            $orders->where('order_date','<=',$endDate);
        }
        
        return $orders->orderBy('id')->get();
    }

    public function getProductsByIds($Ids)
    {
        return Product::whereIn('id', $Ids)->get();
    }


    public function createOrder($request)
    {
        // dd($request);
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => Auth::user()->isAdmin()? $request->user_id: auth()->id(),
                'status' => Order::STATUS_INVENTORY_PENDING,
                'order_date' => now(),
            ]);

            $order->update([
                'warehouse_number' => $order->getTempWhrNumber()."-C",
                'merchant' => $order->user->name
            ]);
            $weight = 0;
            foreach($request->ids as $key=> $productId)
            {
                $product = Product::find($productId);
            
                if ($product->quantity > 0) {
                    $order->products()->attach($product);
                    $product->update([
                        'quantity' => $product->quantity - $request->items[$key]['quantity'],
                    ]);
                    $order->items()->create([
                        'quantity' => $request->items[$key]['quantity'],
                        'sh_code' =>$product->sh_code,
                        'value' => $product->price,
                        'description' => $product->description,
                    ]);
                    $weightInKg = ($product->measurement_unit == 'lbs/in') ? UnitsConverter::poundToKg($product->weight) : $product->weight;
                    $weight += $weightInKg * $request->items[$key]['quantity'];
                }
            }

            if ($order->products->isNotEmpty()) {

                $this->setOrderDemis($order);
                $userWeightPercentage = (setting('weight', 0, $order->user->id) != 0 ? setting('weight', null, $order->user->id) : setting('weight', null, User::ROLE_ADMIN));
                
                $weightToAdd = round(($userWeightPercentage / 100) * $weight, 2);
                $totalWeight = round($weight + $weightToAdd, 2);
                $items = $order->products->toArray();

                $order->update([
                    'customer_reference' => $this->setShCodes($items),
                    'carrier' => $this->setOrderNumbers($items),
                    'tracking_id' => $this->setOrderDescriptions($items),
                    'sender_first_name' => $order->user->name,
                    'sender_last_name' => $order->user->last_name,
                    'weight' => $totalWeight,
                ]);
            }

            DB::commit();

            return true;

        } catch (Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
            return false;
        }
    }

    private function setOrderDemis($order)
    {
        
        $length = (setting('length', 0, $order->user->id) != 0 ? setting('length', null, $order->user->id) : setting('length', null, User::ROLE_ADMIN));
        $width = (setting('width', 0, $order->user->id) != 0 ? setting('width', null, $order->user->id) : setting('width', null, User::ROLE_ADMIN));
        $height = (setting('height', 0, $order->user->id) != 0 ? setting('height', null, $order->user->id) : setting('height', null, User::ROLE_ADMIN));

        $order->update([
            'length' => $length,
            'width' => $width,
            'height' => $height,
            'measurement_unit' => 'kg/cm',
        ]);

        return true;
    }

    public function placeInventoryOrder(Request $request)
    {
        DB::beginTransaction();
        try {

            $order = Order::create([
                'user_id' => Auth::user()->isAdmin()? $request->user_id: auth()->id(),
                'status' => Order::STATUS_PREALERT_TRANSIT,
                'customer_reference' => $this->setShCodes($request->order_items),
                'carrier' => $this->setOrderNumbers($request->order_items),
                'tracking_id' => $this->setOrderDescriptions($request->order_items),
                'weight' => $this->setOrderWeights($request->order_items),
                'length' => 5,
                'width' => 4,
                'height' => 5,
                'measurement_unit' => 'kg/cm',
                'order_date' => now(),
            ]);
            $order->update([
                'warehouse_number' => $order->getTempWhrNumber(false),
            ]);

            foreach ($request->order_items as $item) {

               $product = Product::find($item['id']);
               $order->products()->attach($product);
                $product->update([
                     'quantity' => $product->quantity - $item['quantity'],
                ]);

                $order->items()->create([
                    'quantity' => $item['quantity'],
                    'sh_code' =>$product->sh_code,
                    'value' => $product->price,
                    'description' => $product->description,
                ]);
            }

            DB::commit();

            try {
                Mail::send(new InventoryOrderPlaced($order));
            } catch (\Exception $ex) {
                \Log::info('Inventory Order email error: '.$ex->getMessage());
            }

            return $order;
        } catch (\Exception $ex) {
            DB::rollback();
            $this->error = $ex->getMessage();
        }

    }

    public function getPickupOrders()
    {
        $query = Order::has('products');
        $query = (auth()->user()->isAdmin()) ? $query : $query->where('user_id', auth()->user()->id);
        return $query->where('status','>=',Order::STATUS_INVENTORY_FULFILLED)->orderBy('id','desc')->paginate(50);
    }
    
    private function setShCodes($items)
    {
        $shCodes = [];
        foreach ($items as $item) {
            $shCodes[] = $item['sh_code'];
        }

        return implode(',', $shCodes);
    }

    private function setOrderNumbers($items)
    {
        $orderNumbers = [];
        foreach ($items as $item) {
            $orderNumbers[] = $item['order'];
        }
        return implode(',', $orderNumbers);
    }

    private function setOrderDescriptions($items)
    {
        $orderDescriptions = [];
        foreach ($items as $item) {
            $orderDescriptions[] = $item['description'];
        }
        return implode(',', $orderDescriptions);
    }

    private function setOrderWeights($items)
    {
        $weight = 0;

        foreach ($items as $item) {
            $weight += $item['total_weight'];
        }

        $totalWeight = $weight + ($weight * Product::WEIGHT_PERCENTAGE);

        return number_format($totalWeight, 2);
    }

    public function getError()
    {
        return $this->error;
    }
}
