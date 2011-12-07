<?php

namespace Zend\Cache\Pattern;
use Zend\Cache,
    Zend\Cache\Exception\InvalidArgumentException;

abstract class AbstractPattern implements Cache\Pattern
{

    /**
     * Constructor
     *
     * @param array|Traversable $options
     */
    public function __construct($options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Set pattern options
     *
     * @param array|Traversable $options
     * @return AbstractPattern
     * @throws InvalidArgumentException
     */
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

    /**
     * Get all pattern options
     *
     * @return array
     */
    public function getOptions()
    {
        return array();
    }

}
