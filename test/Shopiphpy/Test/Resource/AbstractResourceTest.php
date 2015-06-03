<?php

namespace Shopiphpy\Test;

use Shopiphpy\ShopifySession;

abstract class AbstractResourceTest extends \PHPUnit_Framework_TestCase
{
    protected $browser;
    protected $shopifySession;

    public function setUp()
    {
        $this->browser = $this->getMockBuilder('Buzz\Browser')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->shopifySession = new ShopifySession('fake_token', 'fake_shop_name', $this->browser);
    }
}
