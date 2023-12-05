<?php declare(strict_types=1);

namespace AmazonSellingPartner\Exception;

final class ApiException extends Exception
{
    /**
     * The deserialized response object.
     */
    protected $responseObject = null;

    /**
     * Constructor.
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param null|string[] $responseHeaders HTTP response header
     * @param null|\stdClass|string $responseBody HTTP decoded body of the server response either as \stdClass or string
     * @param null|\Throwable $previousException
     */
    protected ?array $responseHeaders = null;
    protected $responseBody = null;

    public function __construct(string $message = '', int $code = 0, \Throwable $previousException = null)
    {
        $this->responseHeaders = null; // You can initialize this property if needed
        $this->responseBody = null;   // You can initialize this property if needed

        parent::__construct($message, $code, $previousException);
    }

    /**
     * Gets the HTTP response header.
     *
     * @return null|string[] HTTP response header
     */
    public function getResponseHeaders() : ?array
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
     * Sets the deseralized response object (during deserialization).
     *
     * @param \stdClass|string $obj Deserialized response object
     */
    public function setResponseObject(\stdClass|string $obj) : void
    {
        $this->responseObject = $obj;
    }

    /**
     * Gets the deseralized response object (during deserialization).
     *
     * @return null|\stdClass|string the deserialized response object
     */
    public function getResponseObject()
    {
        return $this->responseObject;
    }
}
