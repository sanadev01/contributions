<?php

declare(strict_types=1);

namespace AmazonSellingPartner;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class HttpFactory
{
    private $requestFactory;
    private $streamFactory;

    public function __construct(RequestFactoryInterface $requestFactory, StreamFactoryInterface $streamFactory)
    {
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    public function createRequest(string $method, string $url) : RequestInterface
    {
        return $this->requestFactory->createRequest($method, $url);
    }

    public function createStreamFromString(string $content) : StreamInterface
    {
        return $this->streamFactory->createStream($content);
    }
}
