<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\State;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use App\Services\Converters\UnitsConverter;

class ConsolidateDomesticLabelRepository
{
    public $consolidatedOrder;
    public $totalOrdersWeight = 0;
    public $totalOrdersLength = 0;
    public $totalOrdersWidth = 0;
    public $totalOrdersHeight = 0;
    public $totalWeightInKg = 0;

    public $errors = [];

    public function getInternationalOrders(array $ids)
    {
        $orders = Order::whereIn('id', $ids)
            ->where('corrios_tracking_code', '!=', null)
            ->get();

        return $orders->filter(function ($order) {

            if ($order->has_second_label) {
                array_push($this->errors, $order->warehouse_number . ' already has a second label');
            }

            if (!$order->is_international) {
                array_push($this->errors, $order->warehouse_number . ' is of US origin');
            }

            return (!$order->has_second_label && $order->is_international);
        });
    }

    public function getTotalWeight($orders)
    {

        $orders->each(function ($order) {
            $this->totalWeightInKg += $order->getWeight('kg');
        });

        return [
            'totalWeightInKg' => $this->totalWeightInKg,
            'totalWeightInLbs' => UnitsConverter::kgToPound($this->totalWeightInKg),
        ];
    }

    public function consolidateOrders($orders)
    {
        foreach ($orders as $order) {
            $this->consolidatedOrderWeightandDimensions($order);
        }

        DB::transaction(function () use ($orders) {
            $this->consolidatedOrder = new Order();
            $this->consolidatedOrder->id = 1;
            $this->consolidatedOrder->user = $orders->first()->user;
            $this->consolidatedOrder->width = $this->totalOrdersWidth;
            $this->consolidatedOrder->height = $this->totalOrdersHeight;
            $this->consolidatedOrder->length = $this->totalOrdersLength;
            $this->consolidatedOrder->weight = $this->totalOrdersWeight;
            $this->consolidatedOrder->measurement_unit = 'lbs/in';
            $this->consolidatedOrder->recipient = $orders->first()->recipient;
            $this->consolidatedOrder->refresh();

            $this->consolidatedOrder->weight = $this->consolidatedOrder->getWeight('kg');
        });

        return $this->consolidatedOrder;
    }

    public function getStates()
    {
        return State::query()->where("country_id", Country::US)->get(["name", "code", "id"]);
    }

    private function consolidatedOrderWeightandDimensions($order)
    {
        $this->totalOrdersWeight += $order->getWeight('lbs');
        $this->totalOrdersLength += ($order->is_weight_in_kg) ? UnitsConverter::cmToIn($order->length) : $order->length;
        $this->totalOrdersWidth += ($order->is_weight_in_kg) ? UnitsConverter::cmToIn($order->width) : $order->width;
        $this->totalOrdersHeight += ($order->is_weight_in_kg) ? UnitsConverter::cmToIn($order->height) : $order->height;

        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
