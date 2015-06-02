<?php

namespace Shopiphpy\Exception;

class ShopifyRequestException extends \Exception
{
    private $errorCode;
    private $reason;

    /**
     * @param int    $errorCode The http response status code
     * @param string $reason    The reason of the failure
     */
    public function __construct($errorCode, $reason)
    {
        $this->errorCode = $errorCode;
        $this->reason = $reason;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @param int    $errorCode The http response status code
     * @param string $reason    The reason of the failure
     *
     * @return ShopifyRequestException
     */
    public static function create($errorCode, $reason)
    {
        switch ($errorCode) {
            case 429:
                return new ShopifyApiRateLimitException($errorCode, $reason);
                break;
            case 404:
                return new ShopifyNotFoundException($errorCode, $reason);
                break;
            default:
                return new self($errorCode, $reason);
                break;
        }
    }
}
