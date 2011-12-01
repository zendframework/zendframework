<?php

namespace Zend\Cache\Pattern;
use Zend\Cache,
    Zend\Cache\Exception\InvalidArgumentException;

abstract class AbstractPattern implements Cache\Pattern
{

    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    public function setOptions($options)
    {
        if (!($options instanceof Traversable) && !is_array($options)) {
            throw new InvalidArgumentException(
                'Options must be an array or an instance of Traversable'
            );
        }

        foreach ($options as $option => $value) {
            $method = 'set'
                    . str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower($option))));
            $this->{$method}($value);
        }

        return $this;
    }

    public function getOptions()
    {
        return array();
    }

}
