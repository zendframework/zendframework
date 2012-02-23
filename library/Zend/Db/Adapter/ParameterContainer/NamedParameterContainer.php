<?php

namespace Zend\Db\Adapter\DriverStatement;

use Iterator;

class NamedParameterContainer implements Iterator, ParameterContainerInterface
{
    const ARRAY_IS_NAMES = 'names';
    const ARRAY_IS_NAMES_AND_VALUES = 'namesAndValues';
    
    protected $values = array();
    protected $errata = array();
    
    public function __construct($array = null, $arrayMode = self::ARRAY_IS_NAMES)
    {
        if ($array !== null && !is_array($array)) {
            throw new \InvalidArgumentException('array parameter must be an array');
        }
        
        if ($array && $arrayMode === self::ARRAY_IS_NAMES) {
            $this->setNames($array);
        } elseif ($array && $arrayMode === self::ARRAY_IS_NAMES_AND_VALUES) {
            $this->setNames(array_keys($array));
            $this->setFromArray($array);
        }
    }
    
    public function setNames(array $names)
    {
        foreach ($names as $name) {
            $this->values[$name] = null;
            $this->errata[$name] = null;
        }
    }
    
    public function offsetSet($name, $value, $errata = null)
    {
        $this->values[$name] = $value;
    }
    
    public function offsetGet($name)
    {
        return (isset($this->values[$name])) ? $this->values[$name] : null;
    }
    
    public function offsetExists($name)
    {
        return isset($this->values[$name]);
    }
    public function offsetUnset($name)
    {
        unset($this->values[$name]);
    }
    
    public function setFromArray(Array $array)
    {
        if (count($array) === 0) {
            return;
        }

        foreach ($array as $name => $value) {
            $this->offsetSet($name, $value);
        }
        return $this;
    }

    public function offsetSetErrata($name, $errata)
    {
        if (!array_key_exists($name, $this->values)) {
            throw new \InvalidArgumentException('A value for the name must exist before assigning errata');
        }
        $this->errata[$name] = $errata;
    }
    
    public function offsetGetErrata($name)
    {
        if (!$this->offsetExists($name)) {
            throw new \InvalidArgumentException('Invalid name for this errata: ' . $name);
        }
        return (isset($this->errata[$name])) ? $this->errata[$name] : null;
    }

    public function offsetHasErrata($name)
    {
        if (!$this->offsetExists($name)) {
            throw new \InvalidArgumentException('Invalid name for this errata: ' . $name);
        }
        return (isset($this->errata[$name]) && $this->errata[$name] !== null);
    }
    
    public function offsetUnsetErrata($name)
    {
        if (!$this->offsetExists($name)) {
            throw new \InvalidArgumentException('Invalid name for this errata: ' . $name);
        }
        unset($this->errata[$name]);
    }
    
    public function getErrataIterator()
    {
        return new \ArrayIterator($this->errata);
    }
    
    public function count()
    {
        return count($this->values);
    }
    
    public function __get($name)
    {
        return $this->offsetGet($name);
    }
    
    public function __set($name, $value)
    {
        return $this->offsetSet($name, $value);
    }
    
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }
    
    public function __unset($name)
    {
        return $this->offsetUnset($name);
    }

    public function toArray()
    {
        return $this->values;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        return next($this->values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return scalar scalar on success, integer
     * 0 on failure.
     */
    public function key()
    {
        return key($this->values);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return (current($this->values) !== false);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->values);
    }
}
