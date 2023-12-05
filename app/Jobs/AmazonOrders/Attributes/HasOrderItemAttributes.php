<?php

namespace App\Jobs\AmazonOrders\Attributes;

use AmazonSellingPartner\Model\Orders\OrderItem;
use App\Models\AmazonOrders\SaleOrderItem;
use App\Models\Product;

trait HasOrderItemAttributes {

    private function _saveOrderItem(OrderItem $order_item): SaleOrderItem {
        /** @var Product $product */
        $product = Product::query()->firstOrCreate([
            'user_id' => $this->sale_order->user_id,
            'sku'     => $order_item->getSellerSKU()
        ], [
            'asin'           => $order_item->getASIN(),
            'title'          => $order_item->getTitle(),
            'marketplace_id' => $this->sale_order->marketplace_id,
        ]);

        /** @var SaleOrderItem $sale_order_item */
        $sale_order_item = SaleOrderItem::query()->firstOrCreate([
            'sale_order_id' => $this->sale_order->id,
            'order_item_id' => $order_item->getOrderItemId(),
            'product_id'    => $product->id,
        ]);

        $data = $this->_getItemData($order_item);
        $sale_order_item->fill($data);
        $sale_order_item->save();

        return $sale_order_item;
    }

    private function _getItemData(OrderItem $order_item): array {
        $buyer_info = $order_item->getBuyerInfo();

        return [
            'quantity_ordered'              => $order_item->getQuantityOrdered(),
            'quantity_shipped'              => $order_item->getQuantityShipped(),
            'number_of_items'               => ($info = $order_item->getProductInfo()) ? $info->getNumberOfItems() : 0,
            'item_price'                    => ($money = $order_item->getItemPrice()) ? $money->getAmount() : 0,
            'item_tax'                      => ($money = $order_item->getItemTax()) ? $money->getAmount() : 0,
            'shipping_price'                => ($money = $order_item->getShippingPrice()) ? $money->getAmount() : 0,
            'shipping_tax'                  => ($money = $order_item->getShippingTax()) ? $money->getAmount() : 0,
            'gift_wrap_price'               => $buyer_info && ($money = $buyer_info->getGiftWrapPrice()) ? $money->getAmount() : 0,
            'gift_wrap_tax'                 => $buyer_info && ($money = $buyer_info->getGiftWrapTax()) ? $money->getAmount() : 0,
            'gift_message_text'             => optional($buyer_info)->getGiftMessageText(),
            'gift_wrap_level'               => optional($buyer_info)->getGiftWrapLevel(),
            'buyer_customized_info'         => ($buyer_info && $info = $buyer_info->getBuyerCustomizedInfo()) ? $info->getCustomizedUrl() : null,
            'shipping_discount'             => ($money = $order_item->getShippingDiscount()) ? $money->getAmount() : 0,
            'shipping_discount_tax'         => ($money = $order_item->getShippingDiscountTax()) ? $money->getAmount() : 0,
            'promotion_discount'            => ($money = $order_item->getPromotionDiscount()) ? $money->getAmount() : 0,
            'promotion_discount_tax'        => ($money = $order_item->getPromotionDiscountTax()) ? $money->getAmount() : 0,
            'promotion_ids'                 => json_encode($order_item->getPromotionIds()),
            'cod_fee'                       => ($money = $order_item->getCodFee()) ? $money->getAmount() : 0,
            'cod_fee_discount'              => ($money = $order_item->getCodFeeDiscount()) ? $money->getAmount() : 0,
            'is_gift'                       => filter_var($order_item->getIsGift(), FILTER_VALIDATE_BOOLEAN),
            'condition_note'                => $order_item->getConditionNote(),
            'condition_id'                  => $order_item->getConditionId(),
            'condition_sub_type_id'         => $order_item->getConditionSubtypeId(),
            'scheduled_delivery_start_date' => parse_date($order_item->getScheduledDeliveryStartDate()),
            'scheduled_delivery_end_date'   => parse_date($order_item->getScheduledDeliveryEndDate()),
            'price_designation'             => $order_item->getPriceDesignation(),
            'serial_number_required'        => filter_var($order_item->getSerialNumberRequired(), FILTER_VALIDATE_BOOLEAN),
            'is_transparency'               => filter_var($order_item->getIsTransparency(), FILTER_VALIDATE_BOOLEAN),
            'ioss_number'                   => $order_item->getIossNumber(),
            'deemed_reseller_category'      => $order_item->getDeemedResellerCategory(),
            'granted_points'                => ($obj = $order_item->getPointsGranted()) ? $obj->__toString() : null,
            'tax_collection'                => ($obj = $order_item->getTaxCollection()) ? $obj->__toString() : null,
        ];
    }
}
