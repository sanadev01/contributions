<?php

namespace App\Services\Converters;

class UnitsConverter
{
    public static function inToCm($inch)
    {
        return round($inch * 2.54, 2);
    }

    public static function cmToIn($cms)
    {
        return round($cms / 2.54, 2);
    }

    public static function kgToPound($kgs)
    {
        return round($kgs * 2.205, 2);
    }

    public static function poundToKg($pounds)
    {
        return round($pounds / 2.205, 2);
    }

    public static function gramsToKg($grams)
    {
        return round($grams / 1000, 2);
    }

    public static function kgToGrams($kgs)
    {
        return round($kgs * 1000, 2);
    }
}
