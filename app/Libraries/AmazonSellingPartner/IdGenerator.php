<?php declare(strict_types=1);

namespace AmazonSellingPartner;

interface IdGenerator
{
    public function generate() : string;
}
