<?php

declare(strict_types=1);

namespace AmazonSellingPartner\Exception;

use Exception;

final class ApiException extends Exception
{
    /**
     * The deserialized response object.
     */
    protected $responseObject = null;

    /**
     * @var null|string[] HTTP response header
     */
    protected $responseHeaders = null;

    /**
     * @var null|\stdClass|string HTTP decoded body of the server response either as \stdClass or string
     */
    protected $responseBody = null;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previousException = null
    ) {
        $this->responseHeaders = null; // You can initialize this property if needed
        $this->responseBody = null;   // You can initialize this property if needed

        parent::__construct($message, $code, $previousException);
    }

    /**
     * Gets the HTTP response header.
     *
     * @return null|string[] HTTP response header
     */
    public function getResponseHeaders(): ?array
    {
        return $this->responseHeaders;
    }

    /**
     * Gets the HTTP body of the server response either as Json or string.
     *
     * @return null|\stdClass|string HTTP body of the server response either as \stdClass or string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * Sets the deserialized response object (during deserialization).
     *
     * @param \stdClass|string $obj Deserialized response object
     */
    public function setResponseObject($obj): void
    {
        $this->responseObject = $obj;
    }

    /**
     * Gets the deserialized response object (during deserialization).
     *
     * @return null|\stdClass|string the deserialized response object
     */
    public function getResponseObject()
    {
        return $this->responseObject;
    }
}
