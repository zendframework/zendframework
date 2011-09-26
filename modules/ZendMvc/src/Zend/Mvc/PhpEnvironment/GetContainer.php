<?php

namespace Zend\Mvc\PhpEnvironment;

use ArrayIterator,
    IteratorAggregate,
    Zend\Stdlib\ParametersDescription;

class GetContainer implements IteratorAggregate, ParametersDescription
{
    /**
     * Constructor
     *
     * Optionally seed the container with an array of values
     * 
     * @param  null|array $values 
     * @return void
     */
    public function __construct(array $values = null)
    {
        if (is_array($values)) {
            $this->fromArray($values);
        }
    }

    /**
     * Populate from native PHP array
     * 
     * @param  array $values 
     * @return void
     */
    public function fromArray(array $values)
    {
        $_GET =& $values;
    }

    /**
     * Populate from query string
     * 
     * @param  string $string 
     * @return void
     */
    public function fromString($string)
    {
        $array = array();
        parse_str($string, $array);
        $this->fromArray($array);
    }

    /**
     * Serialize to native PHP array
     * 
     * @return array
     */
    public function toArray()
    {
        return $_GET;
    }

    /**
     * Serialize to query string
     * 
     * @return string
     */
    public function toString()
    {
        return http_build_query($_GET);
    }

    /**
     * Retrieve by key
     *
     * Returns null if the key does not exist.
     * 
     * @param  string $name 
     * @return mixed
     */
    public function offsetGet($name)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        return null;
    }

    /**
     * Set value by key
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return GetContainer
     */
    public function offsetSet($name, $value)
    {
        $_GET[$name] = $value;
        return $this;
    }

    /**
     * Does the offset exist?
     * 
     * @param  string $name 
     * @return bool
     */
    public function offsetExists($name)
    {
        return isset($_GET[$name]);
    }

    /**
     * Unset a named key
     * 
     * @param  string $name 
     * @return bool
     */
    public function offsetUnset($name)
    {
        if (!isset($this[$name])) {
            return false;
        }
        unset($_GET[$name]);
        return true;
    }

    /**
     * Return a count of keys
     * 
     * @return int
     */
    public function count()
    {
        return count($_GET);
    }

    /**
     * Serialize container
     * 
     * @return string
     */
    public function serialize()
    {
        return serialize($_GET);
    }

    /**
     * Unserialize container
     * 
     * @param  string $data 
     * @return void
     */
    public function unserialize($data)
    {
        $array = unserialize($data);
        $this->fromArray($data);
    }

    /**
     * Create an iterator of the internal data
     * 
     * @return Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($_GET);
    }
    
    /**
     * Convenience method for retrieving a key, using a default value if not found
     *
     * @param  string $name
     * @param  mixed $default optional default value
     * @return mixed
     */
    public function get($name, $default = null)
    {
        $return = $this->offsetGet($name);
        if (null === $return) {
            return $default;
        }
        return $return;
    }
    
    /**
     * Set a value in the container
     *
     * @param  string $name
     * @param  mixed $value
     * @return GetContainer
     */
    public function set($name, $value)
    {
        $this->offsetSet($name, $value);
        return $this;
    }
}
