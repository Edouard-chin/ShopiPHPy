<?php

namespace Shopiphpy\Test;

use Shopiphpy\ShopifyOAuthHelper;

class ShopifyOAuthHelperTest extends \PHPUnit_Framework_TestCase
{
    const SHOP_NAME     = 'dudek';
    const CLIENT_ID     = '123456fake_id';
    const CLIENT_SECRET = '000123_fake_secret';

    private $oAuthHelper;

    public function setUp()
    {
        $this->oAuthHelper = new ShopifyOAuthHelper(self::SHOP_NAME, self::CLIENT_ID, self::CLIENT_SECRET);
     }

    public function testGetLoginUrl()
    {
        $loginUrl = $this->oAuthHelper->createLoginUri(['read_content', 'write_content']);

        $this->assertEquals('https://dudek.myshopify.com/admin/oauth/authorize?client_id=123456fake_id&scope=read_content%2Cwrite_content', $loginUrl);
    }

    public function testUserAllowedApplicationAndShopifySessionIsReturned()
    {
        $queryParameters = [
            'code' => 'fakecode',
            'timestamp' => '1433193615',
            'signature' => 'fakesignature',
            'hmac' => '8a7e00ed8429043904861838ae869b6d27ea84ac780ed1b94e3cbd5565d0454a',
            'shop' => self::SHOP_NAME,
        ];
        $content ='{"access_token": "my_access_token"}';
        $mock = $this->createBuzz(['HTTP/1.1 200 OK'], $content, 'post');
        $shopifySession = $this->oAuthHelper->getShopifySession($queryParameters, $mock);

        $this->assertEquals($shopifySession->getAccessToken(), 'my_access_token');
    }

    /**
    * @expectedException     Shopiphpy\Exception\ShopifyRequestException
    */
    public function testHashHmacValidationFails()
    {
        $queryParameters = [
            'code' => 'fakecode',
            'timestamp' => '1433193615',
            'signature' => 'fakesignature',
            'hmac' => 'fakeHmac',
            'shop' => self::SHOP_NAME,
        ];
        $content ='{"access_token": "my_access_token"}';
        $mock = $this->createBuzz(['HTTP/1.1 200 OK'], $content);
        $shopifySession = $this->oAuthHelper->getShopifySession($queryParameters, $mock);
    }

    /**
    * @expectedException     Shopiphpy\Exception\ShopifyRequestException
    */
    public function testShopifyExceptionIsThrownBecauseQueryParametersAreMissing()
    {
        $queryParameters = [
            'code' => 'fakecode',
            'hmac' => 'fakeHmac',
            'shop' => self::SHOP_NAME,
        ];
        $mock = $this->createBuzz(['HTTP/1.1 200 OK']);
        $shopifySession = $this->oAuthHelper->getShopifySession($queryParameters, $mock);
    }

    /**
    * @expectedException     Shopiphpy\Exception\ShopifyRequestException
    */
    public function testShopifyExceptionIsThrownBecauseRequestFailed()
    {
        $queryParameters = [
            'code' => 'fakecode',
            'timestamp' => '1433193615',
            'signature' => 'fakesignature',
            'hmac' => '8a7e00ed8429043904861838ae869b6d27ea84ac780ed1b94e3cbd5565d0454a',
            'shop' => self::SHOP_NAME,
        ];
        $content ='{"access_token": "my_access_token"}';
        $mock = $this->createBuzz(['HTTP/1.1 400 Bad Request'], $content, 'post');
        $shopifySession = $this->oAuthHelper->getShopifySession($queryParameters, $mock);
    }

    private function createBuzz(array $headers, $content = null, $method = null)
    {
        $response = new \Buzz\Message\Response;
        $response->setHeaders($headers);
        if ($content) {
            $response->setContent($content);
        }
        $mock = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        if ($method) {
            $mock->expects($this->once())
                ->method($method)
                ->will($this->returnValue($response))
            ;
        }

        return $mock;
    }
}
