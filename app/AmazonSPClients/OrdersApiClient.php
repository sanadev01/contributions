<?php

namespace App\AmazonSPClients;

use AmazonSellingPartner\Exception\ApiException;
use AmazonSellingPartner\Model\Orders\GetOrderItemsResponse;
use AmazonSellingPartner\Model\Orders\GetOrderResponse;
use AmazonSellingPartner\Model\Orders\GetOrdersResponse;
use AmazonSellingPartner\Model\Orders\Order;
use AmazonSellingPartner\SellingPartnerSDK;
use Carbon\Carbon;
use Generator;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

class OrdersApiClient extends Client {

    /**
     * @param string $order_id
     * @return Order
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getOrder(string $order_id): Order {
        /** @var GetOrderResponse $response */
        $response = $this->sendRequest(function (SellingPartnerSDK $sdk) use ($order_id) {
            return $sdk->orders()->getOrder(
               $this->getAccessToken($sdk),
               $this->getRegion(),
                $order_id
            );
        });

        return $response->getPayload();
    }

    /**
     * @param Carbon $from_date_time
     * @param Carbon|null $to_date_time
     * @param bool $paginate
     * @return Generator
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getOrders(Carbon $from_date_time, Carbon $to_date_time = null, bool $paginate = true) {
        $next_token = null;
        $from_date_time = $from_date_time->format("Y-m-d\TH:i:s\Z");
        $to_date_time = $to_date_time->format("Y-m-d\TH:i:s\Z");

        do {
            /** @var GetOrdersResponse $response */
            $response = $this->sendRequest(function (SellingPartnerSDK $sdk) use ($from_date_time, $to_date_time, $next_token) {
                return $sdk->orders()->getOrders(
                    $this->getAccessToken($sdk),
                    $this->getRegion(),
                    [$this->user->marketplace->marketplace_id],
                    $from_date_time,
                    $to_date_time,
                    $next_token
                );
            });

            $result = $response->getPayload();

            $orders = $result->getOrders();
            foreach ($orders as $order) {
                yield $order;
            }

            $next_token = $result->getNextToken();

        } while ($paginate && $next_token);
    }

    /**
     * @param $amazon_order_id
     * @return Generator
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getOrderItems($amazon_order_id) {
        $next_token = null;

        do {
            /** @var GetOrderItemsResponse $response */
            $response = $this->sendRequest(function (SellingPartnerSDK $sdk) use ($amazon_order_id, $next_token) {
                return $sdk->orders()->getOrderItems(
                    $this->getAccessToken($sdk),
                    $this->getRegion(),
                    $amazon_order_id,
                    null,
                    $next_token
                );
            });

            $result = optional($response)->getPayload();

            $order_items = optional($result)->getOrderItems() ?? [];

            foreach ($order_items as $order_item) {
                yield $order_item;
            }

            $next_token = optional($result)->getNextToken();

        } while ($next_token);
    }

    /**
     * @param $order_ids
     * @return Generator
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getOrdersById($order_ids): Generator {
        foreach (to_array($order_ids) as $order_id) {
            yield $this->getOrder($order_id);
        }
    }

}
