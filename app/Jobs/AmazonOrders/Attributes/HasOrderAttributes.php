<?php

namespace App\Jobs\AmazonOrders\Attributes;

use AmazonSellingPartner\Exception\ApiException;
use AmazonSellingPartner\Model\Orders\Order;
use App\Models\AmazonOrders\BuyerInfo;
use App\Models\AmazonOrders\SaleOrder;
use App\Models\AmazonOrders\ShipFromAddress;
use App\Models\AmazonOrders\ShipToAddress;
use App\Models\User;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;

trait HasOrderAttributes {

    /** @var User */
    protected $user;

    /**
     * @param Order $order
     * @return SaleOrder
     * @throws ApiException
     * @throws JsonException
     * @throws ClientExceptionInterface
     */
    private function _saveOrder(Order $order): SaleOrder {
        console_log('Amazon Order ID: ' . $order->getAmazonOrderId());

        /** @var SaleOrder $sale_order */
        $sale_order = SaleOrder::query()->firstOrCreate([
            'user_id'         => $this->user->id,
            'amazon_order_id' => $order->getAmazonOrderId()
        ], [
            'marketplace_id' => $this->user->marketplace_id,
        ]);

        $sale_order->fill($this->_getOrderData($order));

        $this->_saveBuyerInfo($order, $sale_order);
        $this->_saveShipFromAddress($order, $sale_order);
        $this->_saveShipToAddress($order, $sale_order);

        $sale_order->save();

        $this->processItemsJob($sale_order);

        $this->_savePayload($sale_order->last_update_date);

        sleep(2);

        return $sale_order;
    }

    private function _getOrderData(Order $order): array {
        return [
            'seller_order_id'                 => $order->getSellerOrderId(),
            'purchase_date'                   => parse_date($order->getPurchaseDate()),
            'last_update_date'                => parse_date($order->getLastUpdateDate()),
            'order_status'                    => $order->getOrderStatus(),
            'fulfillment_channel'             => $order->getFulfillmentChannel(),
            'sales_channel'                   => $order->getSalesChannel(),
            'order_channel'                   => $order->getOrderChannel(),
            'ship_service_level'              => $order->getShipServiceLevel(),
            'order_total'                     => ($money = $order->getOrderTotal()) ? $money->getAmount() : 0,
            'number_of_items_shipped'         => $order->getNumberOfItemsShipped(),
            'number_of_items_unshipped'       => $order->getNumberOfItemsUnshipped(),
            'payment_execution_detail'        => json_encode($order->getPaymentExecutionDetail()),
            'payment_method'                  => $order->getPaymentMethod(),
            'payment_method_details'          => json_encode($order->getPaymentMethodDetails()),
            'shipment_service_level_category' => $order->getShipmentServiceLevelCategory(),
            'easy_ship_shipment_status'       => $order->getEasyShipShipmentStatus(),
            'cba_displayable_shipping_label'  => $order->getCbaDisplayableShippingLabel(),
            'order_type'                      => $order->getOrderType(),
            'earliest_ship_date'              => parse_date($order->getEarliestShipDate()),
            'latest_ship_date'                => parse_date($order->getLatestShipDate()),
            'earliest_delivery_date'          => parse_date($order->getEarliestDeliveryDate()),
            'latest_delivery_date'            => parse_date($order->getLatestDeliveryDate()),
            'is_business_order'               => filter_var($order->getIsBusinessOrder(), FILTER_VALIDATE_BOOLEAN),
            'is_prime'                        => filter_var($order->getIsPrime(), FILTER_VALIDATE_BOOLEAN),
            'is_premium_order'                => filter_var($order->getIsPremiumOrder(), FILTER_VALIDATE_BOOLEAN),
            'is_global_express_enabled'       => filter_var($order->getIsGlobalExpressEnabled(), FILTER_VALIDATE_BOOLEAN),
            'replaced_order_id'               => $order->getReplacedOrderId(),
            'is_replacement_order'            => filter_var($order->getIsReplacementOrder(), FILTER_VALIDATE_BOOLEAN),
            'promise_response_due_date'       => parse_date($order->getPromiseResponseDueDate()),
            'is_estimated_ship_date_set'      => filter_var($order->getIsEstimatedShipDateSet(), FILTER_VALIDATE_BOOLEAN),
            'is_sold_by_ab'                   => filter_var($order->getIsSoldByAB(), FILTER_VALIDATE_BOOLEAN),
            'fulfillment_instruction'         => ($info = $order->getFulfillmentInstruction()) ? $info->getFulfillmentSupplySourceId() : null,
            'is_ispu'                         => filter_var($order->getIsISPU(), FILTER_VALIDATE_BOOLEAN),
        ];
    }

    private function _saveBuyerInfo(Order $order, SaleOrder &$sale_order) {
        $buyer_info = $order->getBuyerInfo();
        if (!$buyer_info) {
            return null;
        }

        BuyerInfo::query()->firstOrCreate([
            'user_id'       => $sale_order->user_id,
            'sale_order_id' => $sale_order->id
        ], [
            'buyer_email'           => $buyer_info->getBuyerEmail(),
            'buyer_name'            => $buyer_info->getBuyerName(),
            'buyer_country'         => $buyer_info->getBuyerCounty(),
            'buyer_tax_info'        => ($info = $buyer_info->getBuyerTaxInfo()) ? $info->__toString() : null,
            'purchase_order_number' => $buyer_info->getPurchaseOrderNumber(),
        ]);
    }

    private function _saveShipFromAddress(Order $order, SaleOrder &$sale_order) {
        $address = $this->_getAddress($order->getDefaultShipFromLocationAddress());
        if ($address) {
            ShipFromAddress::query()->updateOrCreate([
                'user_id'       => $sale_order->user_id,
                'sale_order_id' => $sale_order->id,
            ], $address);
        }
    }

    private function _saveShipToAddress(Order $order, SaleOrder &$sale_order) {
        $address = $this->_getAddress($order->getShippingAddress());
        if ($address) {
            ShipToAddress::query()->updateOrCreate([
                'user_id'       => $sale_order->user_id,
                'sale_order_id' => $sale_order->id,
            ], $address);
        }
    }

    private function _getAddress($address): array {
        if (!$address) return [];

        return [
            'name'            => $address->getName(),
            'address_line1'   => $address->getAddressLine1(),
            'address_line2'   => $address->getAddressLine2(),
            'address_line3'   => $address->getAddressLine3(),
            'city'            => $address->getCity(),
            'country'         => $address->getCounty(),
            'district'        => $address->getDistrict(),
            'state_or_region' => $address->getStateOrRegion(),
            'municipality'    => $address->getMunicipality(),
            'postal_code'     => $address->getPostalCode(),
            'country_code'    => $address->getCountryCode(),
            'phone'           => $address->getPhone(),
            'address_type'    => $address->getAddressType()
        ];
    }

}
