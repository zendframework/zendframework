<?php

namespace Zend\Session\Storage;

class SessionStorage extends ArrayStorage
{
    public function __construct($input = null, $flags = \ArrayObject::ARRAY_AS_PROPS, $iteratorClass = '\\ArrayIterator')
    {
        if ((null === $input) && isset($_SESSION)) {
            $input = $_SESSION;
            if (is_object($input)) {
                $input = (array) $input;
            }
        } elseif (null === $input) {
            $input = array();
        }
        parent::__construct($input, $flags, $iteratorClass);
        $_SESSION = $this;
    }

    public function __destruct()
    {
        $_SESSION = (array) $this->getArrayCopy();
    }
}
