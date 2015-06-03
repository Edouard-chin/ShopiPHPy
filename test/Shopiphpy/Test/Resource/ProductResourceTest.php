<?php

namespace Shopiphpy\Test;

use Shopiphpy\Resource\Product;

class ProductResourceTest extends AbstractResourceTest
{
    /**
     * @dataProvider productList
     */
    public function testRequestingAListOfProductsIsASuccess($products)
    {
        $response = new \Buzz\Message\Response();
        $response->setContent($products);
        $response->setHeaders(['HTTP/1.1 200 OK']);
        $this->browser->expects($this->once())
            ->method('call')
            ->will($this->returnValue($response))
        ;
        $returnedProducts = $this->shopifySession->request('GET', '/admin/products.json', [], Product::getCalledClass());
        $this->assertEquals('Regular bblack tshirt', $returnedProducts[0]->getTitle());
        $this->assertEquals(2, count($returnedProducts[0]->getVariants()));
    }

    public function productList()
    {
        return [
            [
<<<EOT
{
    "products": [
        {
            "body_html": "Just a regular tshirt",
            "created_at": "2015-05-26T19:54:23+02:00",
            "handle": "regular-bblack-tshirt",
            "id": 648433987,
            "product_type": "T-Shirt",
            "published_at": "2015-05-26T19:53:00+02:00",
            "published_scope": "global",
            "template_suffix": null,
            "title": "Regular bblack tshirt",
            "updated_at": "2015-06-02T06:36:54+02:00",
            "vendor": "Dudek",
            "tags": "",
            "variants": [
                {
                    "barcode": "",
                    "compare_at_price": null,
                    "created_at": "2015-05-26T19:54:23+02:00",
                    "fulfillment_service": "manual",
                    "grams": 0,
                    "id": 1811199299,
                    "inventory_management": "shopify",
                    "inventory_policy": "deny",
                    "option1": "Default Title",
                    "option2": null,
                    "option3": null,
                    "position": 1,
                    "price": "10.00",
                    "product_id": 648433987,
                    "requires_shipping": true,
                    "sku": "",
                    "taxable": true,
                    "title": "Default Title",
                    "updated_at": "2015-05-26T19:54:23+02:00",
                    "inventory_quantity": 1,
                    "old_inventory_quantity": 1,
                    "image_id": null,
                    "weight": 0,
                    "weight_unit": "kg"
                },
                {
                    "barcode": "megacode",
                    "compare_at_price": null,
                    "created_at": "2015-05-26T19:54:23+02:00",
                    "fulfillment_service": "manual",
                    "grams": 0,
                    "id": 1811199299,
                    "inventory_management": "shopify",
                    "inventory_policy": "deny",
                    "option1": "Default Title",
                    "option2": null,
                    "option3": null,
                    "position": 1,
                    "price": "10.00",
                    "product_id": 648433987,
                    "requires_shipping": true,
                    "sku": "",
                    "taxable": true,
                    "title": "Default Title",
                    "updated_at": "2015-05-26T19:54:23+02:00",
                    "inventory_quantity": 1,
                    "old_inventory_quantity": 1,
                    "image_id": null,
                    "weight": 0,
                    "weight_unit": "kg"
                }
            ],
            "options": [
                {
                    "id": 775522243,
                    "name": "Title",
                    "position": 1,
                    "product_id": 648433987
                }
            ],
            "images": [],
            "image": null
        }
    ]
}
EOT
            ],
        ];
    }
}
