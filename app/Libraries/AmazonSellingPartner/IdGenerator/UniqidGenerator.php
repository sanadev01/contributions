<?php

declare(strict_types=1);

namespace AmazonSellingPartner\IdGenerator;

use AmazonSellingPartner\IdGenerator;

final class UniqidGenerator implements IdGenerator
{
    public function generate() : string
    {
        return \uniqid('correlation_id_', true);
    }
}
