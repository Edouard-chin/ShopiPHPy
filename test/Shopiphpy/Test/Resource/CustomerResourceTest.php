<?php

namespace Shopiphpy\Test;

use Shopiphpy\Resource\Customer;

class CustomerResourceTest extends AbstractResourceTest
{
	/**
	 * @dataProvider getCustomers
	 */
	public function testRequestingAListOfCustomersIsASuccess($customers)
	{
	    $response = new \Buzz\Message\Response();
	    $response->setContent($customers);
	    $response->setHeaders(['HTTP/1.1 200 OK']);
	    $this->browser->expects($this->once())
	        ->method('call')
	        ->will($this->returnValue($response))
	    ;
	    $returnedCustomers = $this->shopifySession->request('GET', '/admin/customers.json', [], Customer::getCalledClass());
	    $this->assertEquals('Bob', $returnedCustomers[0]->getFirstName());
	    $this->assertEquals('United States', $returnedCustomers[0]->getDefaultAddress()->getCountry());
	    $this->assertEquals('Chestnut Street 92', $returnedCustomers[0]->getAddresses()[0]->getAddressOne());
	}

	public function getCustomers()
	{
		return [
			[
				<<<EOT
				{
				  "customers": [
				    {
				      "accepts_marketing": false,
				      "created_at": "2015-05-27T18:11:09-04:00",
				      "email": "bob.norman@hostmail.com",
				      "first_name": "Bob",
				      "id": 207119551,
				      "last_name": "Norman",
				      "last_order_id": 450789469,
				      "multipass_identifier": null,
				      "note": null,
				      "orders_count": 1,
				      "state": "disabled",
				      "tax_exempt": false,
				      "total_spent": "41.94",
				      "updated_at": "2015-05-27T18:11:09-04:00",
				      "verified_email": true,
				      "tags": "",
				      "last_order_name": "#1001",
				      "default_address": {
				        "address1": "Chestnut Street 92",
				        "address2": "",
				        "city": "Louisville",
				        "company": null,
				        "country": "United States",
				        "first_name": null,
				        "id": 207119551,
				        "last_name": null,
				        "phone": "555-625-1199",
				        "province": "Kentucky",
				        "zip": "40202",
				        "name": "",
				        "province_code": "KY",
				        "country_code": "US",
				        "country_name": "United States",
				        "default": true
				      },
				      "addresses": [
				        {
				          "address1": "Chestnut Street 92",
				          "address2": "",
				          "city": "Louisville",
				          "company": null,
				          "country": "United States",
				          "first_name": null,
				          "id": 207119551,
				          "last_name": null,
				          "phone": "555-625-1199",
				          "province": "Kentucky",
				          "zip": "40202",
				          "name": "",
				          "province_code": "KY",
				          "country_code": "US",
				          "country_name": "United States",
				          "default": true
				        }
				      ]
				    }
				  ]
				}
EOT
			]
		];
	}
}
