<?php

namespace ZendTest\Loader\TestAsset;

use Zend\Di\Locator;

class ServiceLocator implements Locator
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
