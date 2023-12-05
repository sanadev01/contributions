<?php

declare(strict_types=1);

namespace AmazonSellingPartner;

use AmazonSellingPartner\Api\OrdersV0Api;
use AmazonSellingPartner\Api\SellersApi\SellersSDK;
use AmazonSellingPartner\Api\SellersApi\SellersSDKInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

final class SellingPartnerSDK
{
    /**
     * @var array<class-string>
     */
    private array $instances;

    private readonly HttpFactory $httpFactory;

    public function __construct(
        private readonly ClientInterface $httpClient,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly Configuration $configuration,
        private readonly LoggerInterface $logger
    ) {
        $this->instances = [];

        $this->httpFactory = new HttpFactory($requestFactory, $streamFactory);
    }

    public static function create(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        Configuration $configuration,
        LoggerInterface $logger
    ) : self {
        return new self($httpClient, $requestFactory, $streamFactory, $configuration, $logger);
    }

    public function configuration() : Configuration
    {
        return $this->configuration;
    }

    public function oAuth() : OAuth
    {
        return $this->instantiateSDK(OAuth::class);
    }

    public function orders() : OrdersV0Api\OrdersSDKInterface
    {
        return $this->instantiateSDK(OrdersV0Api\OrdersSDK::class);
    }

    public function sellers() : SellersSDKInterface
    {
        return $this->instantiateSDK(SellersSDK::class);
    }

    public function vendor() : VendorSDK
    {
        return $this->instantiateSDK(VendorSDK::class);
    }

    /**
     * @template T
     *
     * @param T $sdkClass
     *
     * @return T
     */
    private function instantiateSDK(string $sdkClass) : string|object
    {
        if (isset($this->instances[$sdkClass])) {
            return $this->instances[$sdkClass];
        }

        $this->instances[$sdkClass] = ($sdkClass === VendorSDK::class)
            ? VendorSDK::create(
                $this->httpClient,
                $this->requestFactory,
                $this->streamFactory,
                $this->configuration,
                $this->logger
            )
            : new $sdkClass(
                $this->httpClient,
                $this->httpFactory,
                $this->configuration,
                $this->logger
            );

        return $this->instances[$sdkClass];
    }
}
