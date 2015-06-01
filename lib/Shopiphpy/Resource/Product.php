<?php

namespace Shopiphpy\Resource;

class Product extends Resource
{
    public function getId()
    {
        return $this->getProperty('id');
    }

    public function getProductType()
    {
        return $this->getProperty('product_type');
    }

    public function getTitle()
    {
        return $this->getProperty('title');
    }

    public function getVariants()
    {
        return $this->getProperty('variants', ProductVariant::getCalledClass());
    }
}
