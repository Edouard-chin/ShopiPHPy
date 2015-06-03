<?php

namespace Shopiphpy\Resource;

class Address extends Resource
{
    /**
     * @return string
     */
    public function getCity()
    {
        return $this->getProperty('city');
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->getProperty('country');
    }

    /**
     * @return string
     */
    public function getAddressOne()
    {
        return $this->getProperty('address1');
    }
}
