<?php

namespace Zend\Session\Storage;

use Zend\Session\Storage as Storable,
    Zend\Session\Exception as SessionException;

class ArrayStorage extends \ArrayObject implements Storable
{
    protected $_immutable = false;

    public function __construct($input = array(), $flags = \ArrayObject::ARRAY_AS_PROPS, $iteratorClass = '\\ArrayIterator')
    {
        parent::__construct($input, $flags, $iteratorClass);
    }

    public function offsetSet($key, $value)
    {
        if ($this->isImmutable()) {
            throw new SessionException('Cannot set key "' . $key . '" as storage is marked immutable');
        }
        if ($this->isLocked($key)) {
            throw new SessionException('Cannot set key "' . $key . '" due to locking');
        }
        return parent::offsetSet($key, $value);
    }

    public function lock($key = null)
    {
        if (null === $key) {
            $this->setMetadata('_READONLY', true);
            return $this;
        }
        if (isset($this[$key])) {
            $this->setMetadata('_LOCKS', array($key => true));
        }

        return $this;
    }

    public function isLocked($key = null)
    {
        if ($this->isImmutable()) {
            // immutable trumps all
            return true;
        }

        if (null === $key) {
            // testing for global lock
            return $this->getMetadata('_READONLY');
        }

        $locks    = $this->getMetadata('_LOCKS');
        $readOnly = $this->getMetadata('_READONLY');

        if ($readOnly && !$locks) {
            // global lock in play; all keys are locked
            return true;
        } elseif ($readOnly && $locks) {
            return array_key_exists($key, $locks);
        }

        // test for individual locks
        if (!$locks) {
            return false;
        }
        return array_key_exists($key, $locks);
    }

    public function unlock($key = null)
    {
        if (null === $key) {
            // Unlock everything
            $this->setMetadata('_READONLY', false);
            $this->setMetadata('_LOCKS', false);
            return $this;
        }

        $locks = $this->getMetadata('_LOCKS');
        if (!$locks) {
            if (!$this->getMetadata('_READONLY')) {
                return $this;
            }
            $array = $this->toArray();
            $keys  = array_keys($array);
            $locks = array_flip($keys);
            unset($array, $keys);
        }

        if (array_key_exists($key, $locks)) {
            unset($locks[$key]);
            $this->setMetadata('_LOCKS', $locks, true);
        }
        return $this;
    }

    public function markImmutable()
    {
        $this->_immutable = true;
        return $this;
    }

    public function isImmutable()
    {
        return $this->_immutable;
    }

    public function setMetadata($key, $value, $overwriteArray = false)
    {
        if ($this->_immutable) {
            throw new SessionException('Cannot set metadata key "' . $key . '" as storage is marked immutable');
        }

        if (!isset($this['__ZF'])) {
            $this['__ZF'] = array();
        }
        if (isset($this['__ZF'][$key]) && is_array($value)) {
            if ($overwriteArray) {
                $this['__ZF'][$key] = $value;
            } elseif (null === $value) {
                // unset($this['__ZF'][$key]) led to "indirect modification...
                // has no effect" errors, so explicitly pulling array and 
                // unsetting key.
                $array = $this['__ZF'];
                unset($array[$key]);
                $this['__ZF'] = $array;
                unset($array);
            } else {
                $this['__ZF'][$key] = array_merge($this['__ZF'][$key], $value);
            }
        } else {
            if ((null === $value) && isset($this['__ZF'][$key])) {
                // unset($this['__ZF'][$key]) led to "indirect modification...
                // has no effect" errors, so explicitly pulling array and 
                // unsetting key.
                $array = $this['__ZF'];
                unset($array[$key]);
                $this['__ZF'] = $array;
                unset($array);
            } elseif (null !== $value) {
                $this['__ZF'][$key] = $value;
            }
        }
        return $this;
    }

    public function getMetadata($key = null)
    {
        if (!isset($this['__ZF'])) {
            return false;
        }

        if (null === $key) {
            return $this['__ZF'];
        }

        if (!array_key_exists($key, $this['__ZF'])) {
            return false;
        }

        return $this['__ZF'][$key];
    }

    public function clear($key = null)
    {
        if (null === $key) {
            $this->exchangeArray(array());
            return $this;
        }

        if (!isset($this[$key])) {
            return $this;
        }

        // Clear key data
        unset($this[$key]);

        // Clear key metadata
        $this->setMetadata($key, null)
             ->unlock($key);

        return $this;
    }

    public function toArray()
    {
        $values = $this->getArrayCopy();
        if (isset($values['__ZF'])) {
            unset($values['__ZF']);
        }
        return $values;
    }

    public function fromArray(array $array)
    {
        $this->exchangeArray($array);
        return $this;
    }
}
