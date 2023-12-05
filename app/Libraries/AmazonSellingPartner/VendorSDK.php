<?php

declare(strict_types=1);

namespace AmazonSellingPartner;

use AmazonSellingPartner\Api\UpdateInventoryApi\VendorDirectFulfillmentInventorySDK;
use AmazonSellingPartner\Api\UpdateInventoryApi\VendorDirectFulfillmentInventorySDKInterface;
use AmazonSellingPartner\Api\VendorInvoiceApi\VendorDirectFulfillmentPaymentsSDK;
use AmazonSellingPartner\Api\VendorInvoiceApi\VendorDirectFulfillmentPaymentsSDKInterface;
use AmazonSellingPartner\Api\VendorOrdersApi\VendorDirectFulfillmentOrdersSDK;
use AmazonSellingPartner\Api\VendorOrdersApi\VendorDirectFulfillmentOrdersSDKInterface;
use AmazonSellingPartner\Api\VendorPaymentsApi\VendorInvoicesSDK;
use AmazonSellingPartner\Api\VendorPaymentsApi\VendorInvoicesSDKInterface;
use AmazonSellingPartner\Api\VendorShippingApi\VendorShipmentsSDK;
use AmazonSellingPartner\Api\VendorShippingApi\VendorShipmentsSDKInterface;
use AmazonSellingPartner\Api\VendorShippingLabelsApi\VendorDirectFulfillmentShippingSDK;
use AmazonSellingPartner\Api\VendorShippingLabelsApi\VendorDirectFulfillmentShippingSDKInterface;
use AmazonSellingPartner\Api\VendorTransactionApi\VendorDirectFulfillmentTransactionsSDK;
use AmazonSellingPartner\Api\VendorTransactionApi\VendorDirectFulfillmentTransactionsSDKInterface;
use AmazonSellingPartner\Api\VendorTransactionApi\VendorTransactionStatusSDK;
use AmazonSellingPartner\Api\VendorTransactionApi\VendorTransactionStatusSDKInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

final class VendorSDK
{
    /**
     * @var array<class-string>
     */
    private array $instances;

    private $httpFactory;

    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        Configuration $configuration,
        LoggerInterface $logger
    ) {
        $this->instances = [];
        $this->httpFactory = new HttpFactory($requestFactory, $streamFactory);

        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->configuration = $configuration;
        $this->logger = $logger;
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

    public function ordersSDK() : VendorDirectFulfillmentOrdersSDKInterface
    {
        return $this->instantiateSDK(VendorDirectFulfillmentOrdersSDK::class);
    }

    public function invoicesSDK() : VendorInvoicesSDKInterface
    {
        return $this->instantiateSDK(VendorInvoicesSDK::class);
    }

    public function shipmentsSDK() : VendorShipmentsSDKInterface
    {
        return $this->instantiateSDK(VendorShipmentsSDK::class);
    }

    public function transactionStatusSDK() : VendorTransactionStatusSDKInterface
    {
        return $this->instantiateSDK(VendorTransactionStatusSDK::class);
    }

    public function directFulfillmentPaymentsSDK() : VendorDirectFulfillmentPaymentsSDKInterface
    {
        return $this->instantiateSDK(VendorDirectFulfillmentPaymentsSDK::class);
    }

    public function directFulfillmentOrdersSDK() : VendorDirectFulfillmentOrdersSDKInterface
    {
        return $this->instantiateSDK(VendorDirectFulfillmentOrdersSDK::class);
    }

    public function directFulfillmentShippingSDK() : VendorDirectFulfillmentShippingSDKInterface
    {
        return $this->instantiateSDK(VendorDirectFulfillmentShippingSDK::class);
    }

    public function directFulfillmentTransactionsSDK() : VendorDirectFulfillmentTransactionsSDKInterface
    {
        return $this->instantiateSDK(VendorDirectFulfillmentTransactionsSDK::class);
    }

    public function directFulfillmentInventorySDK() : VendorDirectFulfillmentInventorySDKInterface
    {
        return $this->instantiateSDK(VendorDirectFulfillmentInventorySDK::class);
    }

    /**
     * @template T
     *
     * @param T $sdkClass
     *
     * @return T
     */
    private function instantiateSDK(string $sdkClass)
    {
        if (isset($this->instances[$sdkClass])) {
            return $this->instances[$sdkClass];
        }

        $this->instances[$sdkClass] = new $sdkClass(
            $this->httpClient,
            $this->httpFactory,
            $this->configuration,
            $this->logger
        );

        return $this->instances[$sdkClass];
    }
}
