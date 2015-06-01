<?php

namespace Shopiphpy;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Shopiphpy\Exception\ShopifyRequestException;

class ShopifyOAuthHelper
{
    private $shopName;
    private $clientId;
    private $clientSecret;
    private $baseUri;

    /**
     * @param string $shopName     The name of your shop
     * @param string $clientId     Your client id crendential
     * @param string $clientSecret Your cliend secret credential
     */
    public function __construct($shopName, $clientId, $clientSecret)
    {
        $this->shopName = $shopName;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->baseUri = "https://{$shopName}.myshopify.com/admin/oauth";
    }

    /**
     * @param array  $scopes      An array of scopes your application needs
     * @param string $callbackUri The callback Url
     *
     * @return string
     */
    public function createLoginUri(array $scopes, $callbackUri = null)
    {
        $params = [
            'client_id' => $this->clientId,
            'scope' => implode(',', $scopes),
            'redirect_uri' => $callbackUri,
        ];

        return "{$this->baseUri}/authorize?".http_build_query($params);
    }

    /**
     * @param \Buzz\Browser $brower          An instance of Buzz
     * @param array         $queryParameters The $_GET parameters
     *
     * @throws ShopifyRequestException If query parameters are incomplete or if hmac signature verification fails
     *
     * @return ShopifySession
     */
    public function getShopifySession(array $queryParameters = [], \Buzz\Browser $browser = null)
    {
        $queryParameters = count($queryParameters) !== 0 ? $queryParameters : $_GET;
        try {
            $authenticRequest = $this->isValidRequest($queryParameters);
        } catch (MissingOptionsException $e) {
            throw new ShopifyRequestException('Missing query parameters:'.$e);
        }

        if ($authenticRequest === false) {
            throw new ShopifyRequestException('HMAC Signature Validation Mismatch');
        }
        $content = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $queryParameters['code'],
        ];
        $browser = $browser ?: new \Buzz\Browser(new \Buzz\Client\Curl());
        $response = $browser->post("{$this->baseUri}/access_token", [], http_build_query($content));
        if (!$response->isSuccessful()) {
            throw new ShopifyRequestException("Request failed, reason: {$response->getReasonPhrase()}");
        }

        return new ShopifySession(json_decode($response->getContent())->access_token, $this->shopName);
    }

    /**
     * @see https://docs.shopify.com/api/authentication/oauth#verification
     *
     * @param array $queryParameters The $_GET parameters
     *
     * @return bool Whether or not the signature is correct
     */
    protected function isValidRequest(array $queryParameters = [])
    {
        $queryParameters = count($queryParameters) !== 0 ? $queryParameters : $_GET;
        $rawString = '';
        $resolver = (new OptionsResolver())
            ->setRequired(['shop', 'code', 'timestamp', 'hmac'])
            ->setDefined('signature')
        ;
        $datas = $resolver->resolve($queryParameters);
        $returnedHmac = $datas['hmac'];
        unset($datas['hmac'], $datas['signature']);
        $rawString = http_build_query($datas);

        return hash_hmac('sha256', $rawString, $this->clientSecret) === $returnedHmac;
    }
}
