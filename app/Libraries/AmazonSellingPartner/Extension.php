<?php declare(strict_types=1);

namespace AmazonSellingPartner;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface Extension
{
    public function preRequest(string $api, string $operation, RequestInterface $request) : void;

    public function postRequest(string $api, string $operation, RequestInterface $request, ResponseInterface $response) : void;
}
