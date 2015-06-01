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
        return $this->createDateTimeObject($this->getProperty('created_at'));
    }

    public function getUpdatedAt()
    {
        return $this->createDateTimeObject($this->getProperty('updated_at'));
    }

    public function getProperty($property, $resource = 'Shopiphpy\Resource\Resource')
    {
        $value = $this->data->$property;
        if (is_scalar($value)) {
            return $value;
        }
        if (!class_exists($resource)) {
            throw new \RuntimeException('Resource type does not exists');
        }

        $value = reset($value);
        $resources = [];
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $resources[] = new $resource($v);
            }
        } else {
            $resources = new $resource($value);
        }

        return $resources;
    }

    private function createDateTimeObject($value)
    {
        return $value ? new \DateTime($value) : null;
    }
}
