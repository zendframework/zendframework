<?php

namespace Zend\Db\Adapter\DriverStatement;

class PositionalParameterContainer extends \SplFixedArray implements ParameterContainerInterface
{
    protected $errata = array();
    
    public function __construct($size = 0, Array $array = array())
    {
        parent::__construct($size);
        if ($array) {
            $this->setFromArray($array);
        }
    }
    
    
    public function offsetSet($position, $value, $errata = null)
    {
        $ret = parent::offsetSet($position, $value);
        if ($errata) {
            $this->offsetSetErrata($position, $errata);
        }
        return $ret;
    }
    
    public function offsetSetErrata($position, $errata)
    {
        if (!array_key_exists($position, $this->values)) {
            throw new \InvalidArgumentException('A value for the position must exist before assigning errata');
        }
        $this->errata[$position] = $errata;
        return $this;
    }
    
    public function offsetGetErrata($position)
    {
        if (!$this->offsetExists($position)) {
            throw new \InvalidArgumentException('Invalid position for this errata');
        }
        return (isset($this->errata[$position])) ?: null;
    }

    public function offsetHasErrata($position)
    {
        if (!$this->offsetExists($position)) {
            throw new \InvalidArgumentException('Invalid position for this errata');
        }
        return (isset($this->errata[$position]) && $this->errata[$position] !== null);
    }
    
    public function offsetUnsetErrata($position)
    {
        if (!$this->offsetExists($position)) {
            throw new \InvalidArgumentException('Invalid position for this errata');
        }
        unset($this->errata[$position]);
    }
    
    public function getErrataIterator()
    {
        return new \ArrayIterator($this->errata);
    }
    
    public function setFromArray(Array $array)
    {
        foreach ($array as $position => $value) {
            $this->offsetSet($position, $value);
        }
    }
    
    public function getIterator()
    {
        return new \ArrayIterator($this->values);
    }

    public function toArray()
    {
        $data = array();
        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }

}
