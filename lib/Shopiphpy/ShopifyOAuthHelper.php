<?php

namespace Shopiphpy;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Shopiphpy\Exception\ShopifyRequestException;

class ShopifyOAuthHelper
{
    private $callbackUri;
    private $shopName;
    private $clientId;
    private $clientSecret;
    private $baseUri;

    public function __construct($shopName, $clientId, $clientSecret, $callbackUri = null)
    {
        $this->shopName = $shopName;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->callbackUri = $callbackUri;
        $this->baseUri = "https://{$shopName}.myshopify.com/admin/oauth";
    }

    public function createLoginUri(array $scopes)
    {
        $params = [
            'client_id' => $this->clientId,
            'scope' => implode(',', $scopes),
            'redirect_uri' => $this->callbackUri,
        ];

        return "{$this->baseUri}/authorize?".http_build_query($params);
    }

    public function getShopifySession(\Buzz $browser = null)
    {
        try {
            $authenticRequest = $this->isValidRequest();
        } catch (UndefinedOptionsException $e) {
            return null;
        }

        if ($authenticRequest === false) {
            throw new ShopifyRequestException('HMAC Signature Validation Mismatch');
        }
        $content = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $_GET['code'],
        ];
        $browser = $browser ?: new \Buzz\Browser(new \Buzz\Client\Curl());
        $response = $browser->post("{$this->baseUri}/access_token", [], http_build_query($content));
        if (!$response->isSuccessful()) {
            throw new ShopifyRequestException("Request failed, reason: {$response->getReasonPhrase()}");
        }

        return new ShopifySession(json_decode($response->getContent())->access_token);
    }

    public function isValidRequest()
    {
        $rawString = '';
        $closure = function ($options, $value) {
            return urlencode($value);
        };

        $resolver = (new OptionsResolver())
            ->setRequired(['shop', 'code', 'timestamp', 'hmac'])
            ->setDefined('signature')
        ;
        $datas = $resolver->resolve($_GET);
        $returnedHmac = $datas['hmac'];
        unset($datas['hmac'],  $datas['signature']);
        $rawString = http_build_query($datas);

        return hash_hmac('sha256', $rawString, $this->clientSecret) === $returnedHmac;
    }
}
