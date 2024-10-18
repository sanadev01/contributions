<?php

namespace App\Services\Correios;

class GetZipcodeGroup
{
    private $zipcode;
    function __construct($zipcode)
    {
        $this->zipcode = str_replace('-', '', $zipcode);
    }

    function getZipcodeGroup()
    {
        if (!$this->zipcode) {
            return null;
        }
        $groups = [
            ["group" => 1, "start" => 1000000,  "end" => 11599999],
            ["group" => 1, "start" => 60000000, "end" => 63999999],
            ["group" => 2, "start" => 11600000, "end" => 19999999],
            ["group" => 2, "start" => 30000000, "end" => 39999999],
            ["group" => 2, "start" => 69900000, "end" => 69999999],
            ["group" => 2, "start" => 72800000, "end" => 72999999],
            ["group" => 2, "start" => 73700000, "end" => 76799999],
            ["group" => 2, "start" => 76800000, "end" => 76999999],
            ["group" => 2, "start" => 77000000, "end" => 77999999],
            ["group" => 2, "start" => 78000000, "end" => 78899999],
            ["group" => 2, "start" => 79000000, "end" => 79999999],
            ["group" => 3, "start" => 20000000, "end" => 28999999],
            ["group" => 3, "start" => 29000000, "end" => 29999999],
            ["group" => 3, "start" => 40000000, "end" => 48999999],
            ["group" => 3, "start" => 49000000, "end" => 49999999],
            ["group" => 3, "start" => 70000000, "end" => 72799999],
            ["group" => 3, "start" => 73000000, "end" => 73699999],
            ["group" => 4, "start" => 80000000, "end" => 87999999],
            ["group" => 4, "start" => 88000000, "end" => 89999999],
            ["group" => 4, "start" => 90000000, "end" => 99999999],
            ["group" => 5, "start" => 50000000, "end" => 56999999],
            ["group" => 5, "start" => 57000000, "end" => 57999999],
            ["group" => 5, "start" => 58000000, "end" => 58999999],
            ["group" => 5, "start" => 59000000, "end" => 59999999],
            ["group" => 5, "start" => 64000000, "end" => 64999999],
            ["group" => 5, "start" => 65000000, "end" => 65999999],
            ["group" => 5, "start" => 66000000, "end" => 68899999],
            ["group" => 5, "start" => 68900000, "end" => 68999999],
            ["group" => 5, "start" => 69000000, "end" => 69299999],
            ["group" => 5, "start" => 69300000, "end" => 69399999],
            ["group" => 5, "start" => 69400000, "end" => 69899999],
        ];
        foreach ($groups as $group) {
            if ($this->zipcode >= $group['start'] && $this->zipcode <= $group['end']) {
                return $group['group'];
            }
        }

        return null;
    }
}
