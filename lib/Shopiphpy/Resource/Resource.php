<?php

namespace Shopiphpy\Resource;

class Resource
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public static function getCalledClass()
    {
        return get_called_class();
    }

    public function getCreatedAt()
    {
        return $this->getProperty('created_at');
    }

    public function getUpdatedAt()
    {
        return $this->getProperty('updated_at');
    }

    public function getProperty($property, $type = 'Shopiphpy\Resource\Resource')
    {
        $value = $this->data->$property;
        if (is_scalar($value)) {
            return $value;
        }

        return new $type($value);
    }
}
