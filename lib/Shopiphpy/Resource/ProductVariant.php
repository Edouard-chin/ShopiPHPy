<?php

namespace Shopiphpy\Resource;

class ProductVariant extends Product
{
    public function getBarCode()
    {
        return $this->getProperty('barcode');
    }

    public function getWeight()
    {
        return $this->getProperty('weight');
    }

    public function getWeightUnit()
    {
        return $this->getProperty('weight_unit');
    }

    public function getSku()
    {
        return $this->getProperty('sku');
    }

    public function getPriceComparison()
    {
        return $this->getProperty('compare_at_price');
    }
}
