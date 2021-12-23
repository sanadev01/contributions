<?php

namespace App\Services\Calculators;

use Exception;

class WeightCalculator
{
    private $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    /**
     * Get Total Normal Weight.
     */
    public function getTotalWeight()
    {
        $weight = 0;
        foreach ($this->items as $item) {
            $weight += ($item['quantity'] ?? 1) * $item['weight'];
        }

        /**
         * round weight to either 0.5 or 1.
         */
        $decimal = $weight - floor($weight);
        $weight = $decimal < 0.5 && $decimal > 0 ? ($weight - $decimal) + 0.5 : ceil($weight);

        return $weight;
    }

    /**
     * Get Volumn/Dimensional Weight.
     */
    public function getTotalVolumnWeight()
    {
        $volumnWeight = 0;

        foreach ($this->items as $item) {
            try {
                $width = explode('x', $item['dimensions'])[0];
                $height = explode('x', $item['dimensions'])[1];
                $length = explode('x', $item['dimensions'])[2];

                $volumn = $width * $height * $length * $item['quantity'];

                $volumnWeight += $volumn / 6000;
            } catch (Exception $ex) {
            }
        }

        return $volumnWeight;
    }

    /**
     * Calculates Volumn weight of given Dimensions.
     * if unit is cm return rate will be in kg
     * if unit is in return rate will be in lbs
     *
     * @param $length
     * @param $width
     * @param $heigt
     * @param string $unit
     */
    public static function getVolumnWeight($length, $width, $heigt, $unit = 'cm')
    {
        if (! in_array($unit, ['in', 'cm'])) {
            throw  new Exception('Invalid Unit.');
        }

        $divisor = $unit == 'in' ? 166 : 6000;
        return round(($length * $width * $heigt) / $divisor,2);
    }

    public static function getUPSVolumnWeight($length, $width, $heigt, $unit = 'in')
    {
        if (! $unit == 'in') {
            throw  new Exception('Invalid Unit.');
        }

        $divisor = 139; //UPS Divisor
        return round(($length * $width * $heigt) / $divisor,2);
    }

    public static function kgToOunce($kgs)
    {
        return $kgs * 35.274;
    }

    public static function ounceToKgs($ounces)
    {
        return $ounces * 0.0283495;
    }

    public static function kgToGrams($kgs)
    {
        return $kgs * 1000;
    }

    public static function gramToKgs($grams)
    {
        return $grams * 0.001;
    }
}
