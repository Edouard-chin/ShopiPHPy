<?php

namespace Shopiphpy;

use Shopiphpy\Exception\ShopifyRequestException;
use Shopiphpy\Resource;

class ShopifySession
{
    const HEADER_TOKEN = 'X-Shopify-Access-Token';
    const HEADER_API_RATE_LIMIT = 'X-Shopify-Shop-Api-Call-Limit';

    private $accessToken;
    private $browser;
    private $baseUri;
    private $apiCallLimit;

    /**
     * @param string        $accessToken A store access token
     * @param string        $shopName    The name of your shop
     * @param \Buzz\Browser $browser     An instance of Browser
     */
    public function __construct($accessToken, $shopName, \Buzz\Browser $browser = null)
    {
        $this->accessToken = $accessToken;
        $this->browser = $browser ?: new \Buzz\Browser(new \Buzz\Client\Curl());
        $this->baseUri = "https://{$shopName}.myshopify.com";
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @see https://docs.shopify.com/api/introduction/api-call-limit
     *
     * @return string
     */
    public function getApiCallLimit()
    {
        return $this->apiCallLimit;
    }

    /**
     * @param string $method     The Request method
     * @param string $path       The path endpoints
     * @param array  $parameters An array of parameters to send with the request
     * @param string $resource   Which type of resource the request should return
     *
     * @throws ShopifyRequestException If the request fails
     * @throws RuntimeException        If resource class does not exists
     *
     * @return array|Resource An array of resource or a single resource
     */
    public function request($method, $path, array $parameters = [])
    {
        $url = $this->baseUri.$path;
        $response = $this->browser->call($url, $method, [self::HEADER_TOKEN => $this->accessToken], http_build_query($parameters));
        if (!$response->isSuccessful()) {
            throw ShopifyRequestException::create($response->getStatusCode(), $response->getReasonPhrase());
        }

        $this->apiCallLimit = $response->getHeader(self::HEADER_API_RATE_LIMIT);
        $rawData = json_decode($response->getContent());
        $data = get_object_vars($rawData);
        $keys = array_keys($data);
        if (!is_array($object = $data[$keys[0]])) {
            return new Resource($object);
        }

        $resources = [];
        foreach ($object as $k => $v) {
            $resources[] = new Resource($v);
        }

        return $resources;
    }
}
