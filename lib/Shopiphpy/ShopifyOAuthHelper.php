<?php

namespace Shopiphpy;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException;
use Shopiphpy\Exception\ShopifyRequestException;

class ShopifyOAuthHelper
{
    private $shopName;
    private $clientId;
    private $clientSecret;
    private $baseUri;

    public function __construct($shopName, $clientId, $clientSecret)
    {
        $this->shopName = $shopName;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->baseUri = "https://{$shopName}.myshopify.com/admin/oauth";
    }

    public function createLoginUri(array $scopes, $callbackUri = null)
    {
        $params = [
            'client_id' => $this->clientId,
            'scope' => implode(',', $scopes),
            'redirect_uri' => $callbackUri,
        ];

        return "{$this->baseUri}/authorize?".http_build_query($params);
    }

    public function getShopifySession(\Buzz $browser = null)
    {
        try {
            $authenticRequest = $this->isValidRequest();
        } catch (UndefinedOptionsException $e) {
            throw new ShopifyRequestException('Missing query parameters:'.$e);
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

        return new ShopifySession(json_decode($response->getContent())->access_token, $this->shopName);
    }

    public function isValidRequest()
    {
        $rawString = '';
        $resolver = (new OptionsResolver())
            ->setRequired(['shop', 'code', 'timestamp', 'hmac'])
            ->setDefined('signature')
        ;
        $datas = $resolver->resolve($_GET);
        $returnedHmac = $datas['hmac'];
        unset($datas['hmac'], $datas['signature']);
        $rawString = http_build_query($datas);

        return hash_hmac('sha256', $rawString, $this->clientSecret) === $returnedHmac;
    }
}
