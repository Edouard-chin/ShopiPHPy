<?php

namespace Shopiphpy;

use Shopiphpy\Exception\ShopifyRequestException;

class ShopifySession
{
    const HEADER_TOKEN = 'X-Shopify-Access-Token';

    private $accessToken;
    private $browser;
    private $baseUri;

    public function __construct($accessToken, $shopName, \Buzz\Browser $browser = null)
    {
        $this->accessToken = $accessToken;
        $this->browser = $browser ?: new \Buzz\Browser(new \Buzz\Client\Curl());
        $this->baseUri = "https://{$shopName}.myshopify.com";
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function request($method, $path, array $parameters = [], $resource = 'Shopiphpy\Resource\Resource')
    {
        $url = $this->baseUri.$path;
        $response = $this->browser->call($url, $method, [self::HEADER_TOKEN => $this->accessToken], http_build_query($parameters));
        if (!$response->isSuccessful()) {
            throw new ShopifyRequestException("Request failed, reason: {$response->getReasonPhrase()}");
        }
        if (!class_exists($resource)) {
            throw new \RuntimeException('Resource type does not exists');
        }
        $decodedContent = json_decode($response->getContent());
        $object = reset($decodedContent);
        $resources = [];
        if (is_array($object)) {
            foreach ($object as $k => $v) {
                $resources[] = new $resource($v);
            }
        } else {
            $resources = new $resource($object);
        }

        return $resources;
    }
}
