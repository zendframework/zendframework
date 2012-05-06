<?php

namespace ZendTest\Loader\TestAsset;

use Zend\Di\LocatorInterface;

class ServiceLocator implements LocatorInterface
{
    protected $services = array();

    public function get($name, array $params = array())
    {
        if (!isset($this->services[$name])) {
            return null;
        }

        return $this->services[$name];
    }

    public function set($name, $object)
    {
        $this->services[$name] = $object;
    }
}
