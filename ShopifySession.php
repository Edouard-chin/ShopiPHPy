<?php

namespace Dudek;

class ShopifySession
{
    private static $apiKey;
    private static $shopName;
    private static $clientId;
    private static $clientSecret;
    private $accessToken;

    public function __construct($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function setDefaultCredentials($apiKey, $shopName, $clientId, $clientSecret)
    {
        self::$apiKey = $apiKey;
        self::$shopName = $shopName;
        self::$shopName = $shopName;
        self::$clientSecret = $clientSecret;
    }
}
