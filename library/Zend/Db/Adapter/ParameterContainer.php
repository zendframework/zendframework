<?php

namespace Zend\Db\Adapter;

use Iterator;

class ParameterContainer implements Iterator, ParameterContainerInterface
{
    protected $data = array();
    protected $errata = array();

    public function __construct(array $data)
    {
        if ($data) {
            $this->setFromArray($data);
        }
    }

    public function offsetExists($nameOrPosition)
    {
        return (isset($this->data[$nameOrPosition]));
    }

    public function offsetGet($nameOrPosition)
    {
        return $this->data[$nameOrPosition];
    }

    public function offsetSet($nameOrPosition, $value, $errata = null)
    {
        if ($nameOrPosition === null) {
            $this->data[] = $value;
            end($this->data);
            $nameOrPosition = key($this->data);
        } else {
            $this->data[$nameOrPosition] = $value;
        }

        $this->errata[$nameOrPosition] = null;
        if ($errata) {
            $this->offsetSetErrata($nameOrPosition, $errata);
        }
    }

    public function offsetUnset($nameOrPosition)
    {
        unset($this->data[$nameOrPosition]);
        return $this;
    }

    public function setFromArray(Array $data)
    {
        foreach ($data as $n => $v) {
            $this->offsetSet($n, $v);
        }
        return $this;
    }

    public function offsetSetErrata($nameOrPosition, $errata)
    {
        if (!array_key_exists($nameOrPosition, $this->errata)) {
            throw new \InvalidArgumentException('Data does not exist for this name/position');
        }
        $this->errata[$nameOrPosition] = $errata;
    }

    public function offsetGetErrata($nameOrPosition)
    {
        if (!array_key_exists($nameOrPosition, $this->errata)) {
            throw new \InvalidArgumentException('Data does not exist for this name/position');
        }
        return $this->errata[$nameOrPosition];
    }

    public function offsetHasErrata($nameOrPosition)
    {
        if (!array_key_exists($nameOrPosition, $this->errata)) {
            throw new \InvalidArgumentException('Data does not exist for this name/position');
        }
        return (isset($this->errata[$nameOrPosition]));
    }

    public function offsetUnsetErrata($nameOrPosition)
    {
        if (!array_key_exists($nameOrPosition, $this->errata)) {
            throw new \InvalidArgumentException('Data does not exist for this name/position');
        }
        $this->errata[$nameOrPosition] = null;
    }

    public function getErrataIterator()
    {
        return new \ArrayIterator($this->errata);
    }

    public function toArray()
    {
        return $this->data;
    }

    public function count()
    {
        return count($this->data);
    }

    public function current()
    {
        return current($this->data);
    }

    public function next()
    {
        return next($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function valid()
    {
        return (current($this->data) !== false);
    }

    public function rewind()
    {
        reset($this->data);
    }
}