<?php

namespace Shopiphpy;

class Resource
{
    private $data;

    /**
     * @param stdClass Json decoded data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Magic method to search for property in the json. Method must find by 'get' be camelized
     * The first element of the arguments tells if value must be returned as a DateTime object
     */
    public function __call($method, $arguments)
    {
        switch (true) {
            case (0 === strpos($method, 'get')):
                $by = substr($method, 3);
                break;

            default:
                throw new \BadMethodCallException(
                    "Undefined method '$method'. The method name must start with 'get'"
                );
        }
        $arguments = array_merge([false], $arguments);
        $fieldName = $this->underscore($by);

        return $this->getProperty($fieldName, $arguments[0]);
    }

    /**
     * A string to underscore (Taken from symfony Container)
     *
     * @param string $id The string to underscore
     *
     * @return string The underscored string
     */
    public static function underscore($id)
    {
        return strtolower(preg_replace(array('/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'), array('\\1_\\2', '\\1_\\2'), strtr($id, '_', '.')));
    }

    /**
     * @param string  $property
     * @param boolean $castToDateTime
     *
     * @return mixed
     */
    public function getProperty($property, $castToDateTime)
    {
        if (!isset($this->data->$property)) {
            return null;
        }
        $value = $this->data->$property;
        if ($value instanceof \stdClass) {
            return new self($value);
        }
        if (is_scalar($value)) {
            return $castToDateTime ? $this->createDateTimeObject($value) : $value;
        }
        $resources = [];
        foreach ($value as $k => $v) {
            $resources[] = new self($v);
        }

        return $resources;
    }

    /**
     * @param string $value
     *
     * @return DateTime|null
     */
    protected function createDateTimeObject($value)
    {
        return $value ? \DateTime::createFromFormat(\DateTime::ISO8601, $value) : null;
    }
}
