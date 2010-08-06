<?php

namespace ZendTest\Form\Element\TestAsset;

class HashSessionContainer
{
    protected static $_hash;

    public function __get($name)
    {
        if ('hash' == $name) {
            return self::$_hash;
        }

        return null;
    }

    public function __set($name, $value)
    {
        if ('hash' == $name) {
            self::$_hash = $value;
        } else {
            $this->$name = $value;
        }
    }

    public function __isset($name)
    {
        if (('hash' == $name) && (null !== self::$_hash))  {
            return true;
        }

        return false;
    }

    public function __call($method, $args)
    {
        switch ($method) {
            case 'setExpirationHops':
            case 'setExpirationSeconds':
                $this->$method = array_shift($args);
                break;
            default:
        }
    }
}
