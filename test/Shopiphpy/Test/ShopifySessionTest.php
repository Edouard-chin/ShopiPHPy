<?php

namespace Shopiphpy\Test;

use Shopiphpy\ShopifySession;
use Shopiphpy\Resource\Product;

class ShopifySessionTest extends \PHPUnit_Framework_TestCase
{
    private $browser;
    private $shopifySession;

    public function setUp()
    {
        $this->browser = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->shopifySession = new ShopifySession('fake_token', 'fake_shop_name', $this->browser);
    }

    /**
     * @expectedException     Shopiphpy\Exception\ShopifyApiRateLimitException
     */
    public function testRequestingApiFailsBecauseApiRateCallLimitExceed()
    {
        $response = new \Buzz\Message\Response();
        $response->setHeaders(['HTTP/1.1 429 Too Many Requests']);
        $this->browser->expects($this->once())
            ->method('call')
            ->will($this->returnValue($response))
        ;
        $returnedProducts = $this->shopifySession->request('GET', '/admin/products.json', [], Product::getCalledClass());
    }

    /**
     * @expectedException     Shopiphpy\Exception\ShopifyNotFoundException
     */
    public function testRequestingApiFailsBecausePathWasNotFound()
    {
        $response = new \Buzz\Message\Response();
        $response->setHeaders(['HTTP/1.1 404 Not Found']);
        $this->browser->expects($this->once())
            ->method('call')
            ->will($this->returnValue($response))
        ;
        $returnedProducts = $this->shopifySession->request('GET', '/admin/products.json', [], Product::getCalledClass());
    }
}
