<?php

namespace App\Services\Calculators;

use App\Models\Order;
use function json_encode;
use App\Services\Converters\UnitsConverter;

abstract class AbstractRateCalculator
{
    const SERVICE_PACKAGE_PLUS = 'PACKAGE_PLUS';
    const SERVICE_LEVE = 'leve';
    const SERVICE_BPS = 'BPS';

    public $serviceId;

    private $name;

    protected $order;

    protected $length;
    protected $width;
    protected $height;

    protected $originalWeight;

    protected $weight;

    protected $shipment;

    protected $address;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->shipment = $order->shipment;
        $this->address = $order->address;

        $this->initializeDims();

        $this->weight = $this->calculateWeight();
    }

    abstract public function isAvailable();

    abstract public function getRate($addProfit = true);

    abstract public function weightUnit();

    abstract public function weightInDefaultUnit();

    private function initializeDims()
    {
        if ($this->shipment->unit == 'lbs/in') {
            $this->length = UnitsConverter::inToCm($this->shipment->length);
            $this->width = UnitsConverter::inToCm($this->shipment->width);
            $this->height = UnitsConverter::inToCm($this->shipment->height);
            $this->originalWeight = UnitsConverter::poundToKg($this->shipment->weight);
        } else {
            $this->length = $this->shipment->length;
            $this->width = $this->shipment->width;
            $this->height = $this->shipment->height;
            $this->originalWeight = $this->shipment->weight;
        }
    }

    private function calculateWeight()
    {
        $volumnWeight = WeightCalculator::getVolumnWeight($this->length, $this->width, $this->height);

        return $volumnWeight > $this->originalWeight ? $volumnWeight : $this->originalWeight;
    }

    public function getProfitOn($cost)
    {
        $profitPercentage = setting($this->getServiceId(), 0, null, true);

        $profitAmount = ($cost * $profitPercentage) / 100;

        return $profitAmount;
    }

    protected function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return float|int
     */
    public function getWeight($unit = 'kg')
    {
        if ($unit == 'kg') {
            return $this->weight;
        }

        if ($unit == 'lbs') {
            return UnitsConverter::kgToPound($this->weight);
        }

        return  'Invalid Unit';
    }

    /**
     * @return mixed
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param mixed $serviceId
     */
    public function setServiceId($serviceId) : void
    {
        $this->serviceId = $serviceId;
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
