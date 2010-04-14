<?php

namespace Zend\Session\Storage;

class SessionStorage extends ArrayStorage
{
    public function __construct($input = null, $flags = \ArrayObject::ARRAY_AS_PROPS, $iteratorClass = '\\ArrayIterator')
    {
        $resetSession = true;
        if ((null === $input) && isset($_SESSION)) {
            $input = $_SESSION;
            if (is_object($input) && $_SESSION instanceof \ArrayObject) {
                $resetSession = false;
            } elseif (is_object($input) && !$_SESSION instanceof \ArrayObject) {
                $input = (array) $input;
            }
        } elseif (null === $input) {
            $input = array();
        }
        parent::__construct($input, $flags, $iteratorClass);
        if ($resetSession) {
            $_SESSION = $this;
        }
    }

    public function __destruct()
    {
        $_SESSION = (array) $this->getArrayCopy();
    }

    public function fromArray(array $array)
    {
        $this->exchangeArray($array);
        if ($_SESSION !== $this) {
            $_SESSION = $this;
        }
        return $this;
    }

    public function markImmutable()
    {
        $this['_IMMUTABLE'] = true;
    }

    public function isImmutable()
    {
        return (isset($this['_IMMUTABLE']) && $this['_IMMUTABLE']);
    }
}
