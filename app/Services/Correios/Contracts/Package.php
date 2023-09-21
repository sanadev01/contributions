<?php

namespace App\Services\Correios\Contracts;

interface Package{

    public function getDistributionModality(): int;
    public function getService() : int;
}
