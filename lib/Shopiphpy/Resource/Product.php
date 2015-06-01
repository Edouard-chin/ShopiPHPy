<?php

namespace Shopiphpy\Resource;

class Product extends Resource
{
    /**
     * @return string
     */
    public function getId()
    {
        return $this->getProperty('id');
    }

    /**
     * @return string
     */
    public function getProductType()
    {
        return $this->getProperty('product_type');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->getProperty('title');
    }

    /**
     * Returns a single on an array of ProductVariant object
     *
     * @return array|ProductVariant
     */
    public function getVariants()
    {
        return $this->getProperty('variants', ProductVariant::getCalledClass());
    }
}
