<?php

declare(strict_types=1);

namespace AmazonSellingPartner\Model\Orders;

use AmazonSellingPartner\Exception\AssertionException;
use AmazonSellingPartner\ModelInterface;
use AmazonSellingPartner\ObjectSerializer;

/**
 * Selling Partner API for Orders.
 *
 * The Selling Partner API for Orders helps you programmatically retrieve order information. These APIs let you develop fast, flexible, custom applications in areas like order synchronization, order research, and demand-based decision support tools.
 *
 * The version of the OpenAPI document: v0
 *
 * This class was auto-generated by https://openapi-generator.tech
 * Do not change it, it will be overwritten with next execution of /bin/generate.sh
 *
 * @implements \ArrayAccess<TKey, TValue>
 *
 * @template TKey int|null
 * @template TValue mixed|null
 */
class OrderItemsBuyerInfoList implements \ArrayAccess, \JsonSerializable, \Stringable, ModelInterface
{
    final public const DISCRIMINATOR = null;

    /**
     * The original name of the model.
     *
     * @var string
     */
    protected static string $openAPIModelName = 'OrderItemsBuyerInfoList';

    /**
     * Array of property to type mappings. Used for (de)serialization.
     *
     * @var string[]
     */
    protected static array $openAPITypes = [
        'order_items' => '\AmazonSellingPartner\Model\Orders\OrderItemBuyerInfo[]',
        'next_token' => 'string',
        'amazon_order_id' => 'string',
    ];

    /**
     * Array of property to format mappings. Used for (de)serialization.
     *
     * @var string[]
     *
     * @phpstan-var array<string, string|null>
     *
     * @psalm-var array<string, string|null>
     */
    protected static array $openAPIFormats = [
        'order_items' => null,
        'next_token' => null,
        'amazon_order_id' => null,
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name.
     *
     * @var string[]
     */
    protected static array $attributeMap = [
        'order_items' => 'OrderItems',
        'next_token' => 'NextToken',
        'amazon_order_id' => 'AmazonOrderId',
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses).
     *
     * @var string[]
     */
    protected static array $setters = [
        'order_items' => 'setOrderItems',
        'next_token' => 'setNextToken',
        'amazon_order_id' => 'setAmazonOrderId',
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests).
     *
     * @var string[]
     */
    protected static array $getters = [
        'order_items' => 'getOrderItems',
        'next_token' => 'getNextToken',
        'amazon_order_id' => 'getAmazonOrderId',
    ];

    /**
     * Associative array for storing property values.
     *
     * @var mixed[]
     */
    protected array $container = [];

    /**
     * Constructor.
     *
     * @param null|mixed[] $data Associated array of property values
     *                           initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['order_items'] = $data['order_items'] ?? null;
        $this->container['next_token'] = $data['next_token'] ?? null;
        $this->container['amazon_order_id'] = $data['amazon_order_id'] ?? null;
    }

    /**
     * Array of property to type mappings. Used for (de)serialization.
     */
    public static function openAPITypes() : array
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization.
     */
    public static function openAPIFormats() : array
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name.
     */
    public static function attributeMap() : array
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses).
     */
    public static function setters() : array
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests).
     */
    public static function getters() : array
    {
        return self::$getters;
    }

    /**
     * Gets the string presentation of the object.
     */
    public function __toString() : string
    {
        return (string) \json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * The original name of the model.
     */
    public function getModelName() : string
    {
        return self::$openAPIModelName;
    }

    /**
     * Validate all properties.
     *
     * @throws AssertionException
     */
    public function validate() : void
    {
        if ($this->container['order_items'] === null) {
            throw new AssertionException("'order_items' can't be null");
        }

        if ($this->container['amazon_order_id'] === null) {
            throw new AssertionException("'amazon_order_id' can't be null");
        }
    }

    /**
     * Gets order_items.
     *
     * @return \AmazonSellingPartner\Model\Orders\OrderItemBuyerInfo[]
     */
    public function getOrderItems() : array
    {
        return $this->container['order_items'];
    }

    /**
     * Sets order_items.
     *
     * @param \AmazonSellingPartner\Model\Orders\OrderItemBuyerInfo[] $order_items a single order item's buyer information list
     */
    public function setOrderItems(array $order_items) : self
    {
        $this->container['order_items'] = $order_items;

        return $this;
    }

    /**
     * Gets next_token.
     */
    public function getNextToken() : ?string
    {
        return $this->container['next_token'];
    }

    /**
     * Sets next_token.
     *
     * @param null|string $next_token when present and not empty, pass this string token in the next request to return the next response page
     */
    public function setNextToken(?string $next_token) : self
    {
        $this->container['next_token'] = $next_token;

        return $this;
    }

    /**
     * Gets amazon_order_id.
     */
    public function getAmazonOrderId() : string
    {
        return $this->container['amazon_order_id'];
    }

    /**
     * Sets amazon_order_id.
     *
     * @param string $amazon_order_id an Amazon-defined order identifier, in 3-7-7 format
     */
    public function setAmazonOrderId(string $amazon_order_id) : self
    {
        $this->container['amazon_order_id'] = $amazon_order_id;

        return $this;
    }

    /**
     * Returns true if offset exists. False otherwise.
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @return null|mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset) : mixed
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     */
    public function offsetSet($offset, $value) : void
    {
        if (null === $offset) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     */
    public function offsetUnset($offset) : void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed returns data which can be serialized by json_encode(), which is a value
     *               of any type other than a resource
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize() : string
    {
        return \json_encode(ObjectSerializer::sanitizeForSerialization($this), JSON_THROW_ON_ERROR);
    }

    /**
     * Gets a header-safe presentation of the object.
     */
    public function toHeaderValue() : string
    {
        return \json_encode(ObjectSerializer::sanitizeForSerialization($this), JSON_THROW_ON_ERROR);
    }
}
