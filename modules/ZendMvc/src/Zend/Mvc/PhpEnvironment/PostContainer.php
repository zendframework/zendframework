<?php

namespace Zend\Mvc\PhpEnvironment;

use ArrayIterator,
    IteratorAggregate,
    Zend\Stdlib\ParametersDescription;

class PostContainer implements IteratorAggregate, ParametersDescription
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
        $_POST =& $values;
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
        return $_POST;
    }

    /**
     * Serialize to query string
     * 
     * @return string
     */
    public function toString()
    {
        return http_build_query($_POST);
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
        if (isset($_POST[$name])) {
            return $_POST[$name];
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
        $_POST[$name] = $value;
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
        return isset($_POST[$name]);
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
        unset($_POST[$name]);
        return true;
    }

    /**
     * Return a count of keys
     * 
     * @return int
     */
    public function count()
    {
        return count($_POST);
    }

    /**
     * Serialize container
     * 
     * @return string
     */
    public function serialize()
    {
        return serialize($_POST);
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
        return new ArrayIterator($_POST);
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

