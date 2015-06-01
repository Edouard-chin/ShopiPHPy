<?php

namespace Shopiphpy\Resource;

class ProductVariant extends Product
{
    /**
     * @return string
     */
    public function getBarCode()
    {
        return $this->getProperty('barcode');
    }

    /**
     * @param int $numeric  Whether or not the returned value should be casted
     *
     * @return string|int
     */
    public function getWeight($numeric = false)
    {
        return $numeric ? (float) $this->getProperty('weight') : $this->getProperty('weight');
    }

    /**
     * @return string
     */
    public function getWeightUnit()
    {
        return $this->getProperty('weight_unit');
    }

    /**
     * @return string
     */
    public function getSku()
    {
        return $this->getProperty('sku');
    }

    /**
     * @return string
     */
    public function getPriceComparison()
    {
        return $this->getProperty('compare_at_price');
    }
}
