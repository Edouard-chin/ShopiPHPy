<?php

namespace Shopiphpy\Resource;

class Customer extends Resource
{
    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->getProperty('first_name');
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->getProperty('last_name');
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getProperty('email');
    }

    /**
     * @return boolean
     */
    public function isAcceptingMarketing()
    {
        return $this->getProperty('accepts_marketing');
    }

    /**
     * @return float
     */
    public function getTotalSpend()
    {
        return $this->getProperty('total_spent');
    }

    /**
     * @return boolean
     */
    public function isEmailVerified()
    {
        return $this->getProperty('verified_email');
    }

    /**
     * @return Address An instance of Address resource
     */
    public function getDefaultAddress()
    {
        return $this->getProperty('default_address', Address::getCalledClass());
    }

    /**
     * @return Array  An array of Address resources
     */
    public function getAddresses()
    {
        return $this->getProperty('addresses', Address::getCalledClass());
    }
}
